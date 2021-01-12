<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Controller\Adminhtml\ProductTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductTechNew\Model\Product as SitTechProduct;
use SIT\ProductTechNew\Model\ProductFactory as SitTechProductFactory;
use SIT\ProductTechNew\Model\ProductTechnologyFactory;

class Save extends Action {
	/**
	 * @var ProductTechnologyFactory
	 */
	protected $_productTechnologyFactory;
	/**
	 * @var SitTechProduct
	 */
	protected $sitTechProduct;
	/**
	 * @var SitTechProductFactory
	 */
	protected $sitTechProductFactory;

	/**
	 * [__construct description]
	 * @param Context                  $context                  [description]
	 * @param ProductTechnologyFactory $productTechnologyFactory [description]
	 * @param SitTechProduct           $sitTechProduct           [description]
	 * @param SitTechProductFactory    $sitTechProductFactory    [description]
	 */
	public function __construct(
		Context $context,
		ProductTechnologyFactory $productTechnologyFactory,
		SitTechProduct $sitTechProduct,
		SitTechProductFactory $sitTechProductFactory
	) {
		$this->_productTechnologyFactory = $productTechnologyFactory;
		$this->sitTechProduct = $sitTechProduct;
		$this->sitTechProductFactory = $sitTechProductFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductTechNew::producttechnology');
	}

	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$storeId = (int) $this->getRequest()->getParam('store_id');
		$data = $this->getRequest()->getParams();
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			$params = [];
			$producttechnologyData = $this->_productTechnologyFactory->create();
			$producttechnologyData->setStoreId($storeId);
			$params['store'] = $storeId;
			if (empty($data['entity_id'])) {
				$data['entity_id'] = null;
			} else {
				$producttechnologyData->load($data['entity_id']);
				$params['entity_id'] = $data['entity_id'];
			}
			$producttechnologyData->addData($data);
			$this->_eventManager->dispatch(
				'sit_producttechnew_producttechnew_prepare_save',
				['object' => $this->_productTechnologyFactory, 'request' => $this->getRequest()]
			);

			try {
				$producttechnologyData->save();
				if (isset($data['producttechnew_products'])
					&& is_string($data['producttechnew_products'])) {
					$productsId = (array) json_decode($data['producttechnew_products']);
					$this->insertProductIds($producttechnologyData->getId(), $productsId);
				}
				$this->messageManager->addSuccessMessage(__('You saved this record.'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$params['entity_id'] = $producttechnologyData->getId();
					$params['_current'] = true;
					return $resultRedirect->setPath('*/*/edit', $params);
				}
				return $resultRedirect->setPath('*/*/');
			} catch (\Exception $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
				$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
			}

			$this->_getSession()->setFormData($this->getRequest()->getPostValue());
			return $resultRedirect->setPath('*/*/edit', $params);
		}
		return $resultRedirect->setPath('*/*/');
	}
	private function insertProductIds($questionId, $productsId) {
		$tempSitTechProduct = $this->sitTechProduct;
		$collection = $tempSitTechProduct->getCollection()->addFieldToFilter("producttechnology_id", ["eq" => $questionId]);
		foreach ($collection->getData() as $value) {
			$tempSitTechProduct->load($value["rel_id"]);
			$tempSitTechProduct->delete();
			$tempSitTechProduct->unsetData();
		}
		try {
			foreach ($productsId as $key => $value) {
				/**
				 * Set position value null when position field value blank : MD [START][26-03-2019]
				 */
				if ($value != "" && $value >= 0) {
					$fields = ["producttechnology_id" => $questionId, "product_id" => $key, "position" => $value];
				} else {
					$fields = ["producttechnology_id" => $questionId, "product_id" => $key, "position" => null];
				}
				/**
				 * Set position value null when position field value blank : MD [END][26-03-2019]
				 */
				$this->insertProduct($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}
	private function insertProduct($productArray) {
		$save = $this->sitTechProductFactory->create();
		$save->setData($productArray);
		$save->save();
	}
	private function checkUrlKey($entityId, $urlKey) {
		$model = $this->_productTechnologyFactory->create()->getCollection()->addFieldToFilter("url_key", ["like" => $urlKey])->addFieldToFilter("entity_id", ["neq" => $entityId])->setOrder("producttechnology_id", "DESC")->getFirstItem();
		if (!empty($model->getData())) {
			return 0;
		} else {
			return 1;
		}
	}

}
