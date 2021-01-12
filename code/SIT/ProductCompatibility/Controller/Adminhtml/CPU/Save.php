<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\CPU;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\Product as SitProductComp;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;
use SIT\ProductCompatibility\Model\ProductFactory as SitProductCompFactory;

class Save extends Action {

	/**
	 * @var ProductCompatibilityFactory
	 */
	protected $productcompFactory;

	/**
	 * @var SitProductComp
	 */
	protected $sitProductCompProduct;

	/**
	 * @var SitProductCompFactory
	 */
	protected $sitProductCompFactory;

	/**
	 * @var \Magento\Eav\Model\Entity\Attribute
	 */
	protected $entityAttribute;

	/**
	 * @var \Magento\Eav\Setup\EavSetupFactory
	 */
	protected $eavSetupFactory;

	/**
	 * @var \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute
	 */
	protected $attributeFactory;

	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
	 */
	protected $attributeOptionCollectionFactory;

	/**
	 * @var ProductCompHelper
	 */
	protected $prodCompHelper;

	/**
	 * [__construct description]
	 * @param Context                                                                    $context                          [description]
	 * @param ProductCompatibilityFactory                                                $productcompFactory               [description]
	 * @param SitProductComp                                                             $sitProductCompProduct            [description]
	 * @param SitProductCompFactory                                                      $sitProductCompFactory            [description]
	 * @param \Magento\Eav\Model\Entity\Attribute                                        $entityAttribute                  [description]
	 * @param \Magento\Eav\Setup\EavSetupFactory                                         $eavSetupFactory                  [description]
	 * @param \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute                $attributeFactory                 [description]
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory [description]
	 * @param ProductCompHelper                                                          $prodCompHelper                   [description]
	 */
	public function __construct(
		Context $context,
		ProductCompatibilityFactory $productcompFactory,
		SitProductComp $sitProductCompProduct,
		SitProductCompFactory $sitProductCompFactory,
		\Magento\Eav\Model\Entity\Attribute $entityAttribute,
		\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
		\SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory,
		ProductCompHelper $prodCompHelper
	) {
		$this->productcompFactory = $productcompFactory;
		$this->sitProductCompProduct = $sitProductCompProduct;
		$this->sitProductCompFactory = $sitProductCompFactory;
		$this->entityAttribute = $entityAttribute;
		$this->eavSetupFactory = $eavSetupFactory;
		$this->attributeFactory = $attributeFactory;
		$this->attributeOptionCollectionFactory = $attributeOptionCollectionFactory;
		$this->prodCompHelper = $prodCompHelper;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductCompatibility::cpu');
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
			$productcompInstance = $this->productcompFactory->create();
			$productcompInstance->setStoreId($storeId);
			$params['store'] = $storeId;
			$urlKey = str_replace(' ', '_', $data['comp_manufacture']) . "_" . str_replace(' ', '_', $data['comp_model']);
			if (empty($data['entity_id'])) {
				$data["url_key"] = $urlKey;
				$data['entity_id'] = null;
			} else {
				$productcompInstance->load($data['entity_id']);
				$params['entity_id'] = $data['entity_id'];
				$checkUrl = $this->checkUrlKey($data['entity_id'], $urlKey);
				if ($checkUrl) {
					$data["url_key"] = $urlKey;
				}
			}
			$socketInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
			$socketId = $socketInfo->getAttributeId();
			$socketAllOption = $this->prodCompHelper->getAttributeOptionAll($socketId);

			$manufInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
			$manufId = $manufInfo->getAttributeId();
			$manufAllOption = $this->prodCompHelper->getAttributeOptionAll($manufId);

			$modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
			$modelId = $modelInfo->getAttributeId();
			$modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);

			$seriesInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SERIES);
			$seriesId = $seriesInfo->getAttributeId();
			$seriesAllOption = $this->prodCompHelper->getAttributeOptionAll($seriesId);

			$compTypeInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
			$compTypeId = $compTypeInfo->getAttributeId();

			$socketAttrCheck = $this->checkAttrExist($socketAllOption, ProductCompHelper::COMP_SOCKET, trim($data['comp_socket']));
			$manufAttrCheck = $this->checkAttrExist($manufAllOption, ProductCompHelper::COMP_MANUFACTURE, trim($data['comp_manufacture']));
			$modelAttrCheck = $this->checkAttrExist($modelAllOption, ProductCompHelper::COMP_MODEL, trim($data['comp_model']));
			$seriesAttrCheck = $this->checkAttrExist($seriesAllOption, ProductCompHelper::COMP_SERIES, trim($data['comp_series']));

			$socket_after_save = $this->prodCompHelper->getAttributeOptionAll($socketId);
			$manuf_after_save = $this->prodCompHelper->getAttributeOptionAll($manufId);
			$model_after_save = $this->prodCompHelper->getAttributeOptionAll($modelId);
			$series_after_save = $this->prodCompHelper->getAttributeOptionAll($seriesId);
			$compType_after_save = $this->prodCompHelper->getAttributeOptionAll($compTypeId);

			$data['comp_socket'] = $this->prodCompHelper->getAttrOptionId($socket_after_save, trim($data['comp_socket']));
			$data['comp_manufacture'] = $this->prodCompHelper->getAttrOptionId($manuf_after_save, trim($data['comp_manufacture']));
			$data['comp_model'] = $this->prodCompHelper->getAttrOptionId($model_after_save, trim($data['comp_model']));
			$data['comp_series'] = $this->prodCompHelper->getAttrOptionId($series_after_save, trim($data['comp_series']));
			$data['comp_type'] = $this->prodCompHelper->getAttrOptionId($compType_after_save, trim($data['comp_type']));

			$productcompInstance->addData($data);
			$this->_eventManager->dispatch(
				'sit_productcompatibility_cpu_prepare_save',
				['object' => $this->productcompFactory, 'request' => $this->getRequest()]
			);

			try {
				$productcompInstance->save();
				if (isset($data['productcompatibility_cpu_products'])
					&& is_string($data['productcompatibility_cpu_products'])) {
					$productsId = (array) json_decode($data['productcompatibility_cpu_products']);
					$this->insertProductIds($productcompInstance->getId(), $productsId);
				}
				$this->messageManager->addSuccessMessage(__('You saved this record.'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$params['entity_id'] = $productcompInstance->getId();
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

	/**
	 * Insert selected product ids and position of associated products
	 *
	 * @param  [type] $questionId [description]
	 * @param  [type] $productsId [description]
	 * @return [type]             [description]
	 */
	private function insertProductIds($questionId, $productsId) {
		$tempSitProductComp = $this->sitProductCompProduct;
		$collection = $tempSitProductComp->getCollection()->addFieldToFilter("productcompatibility_id", ["eq" => $questionId]);
		foreach ($collection->getData() as $value) {
			$tempSitProductComp->load($value["rel_id"]);
			$tempSitProductComp->delete();
			$tempSitProductComp->unsetData();
		}
		try {
			foreach ($productsId as $key => $value) {
				/**
				 * Set position value null when position field value blank : MD [START][26-03-2019]
				 */
				if ($value != "" && $value >= 0) {
					if (is_numeric($value)) {
						$fields = ["productcompatibility_id" => $questionId, "product_id" => $key, "position" => $value];
					} else {
						$fields = ["productcompatibility_id" => $questionId, "product_id" => $key, "position" => $value];
						$this->messageManager->addWarning('You have entered text value for product position. By default set value 0');
					}
				} else {
					$fields = ["productcompatibility_id" => $questionId, "product_id" => $key, "position" => null];
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

	/**
	 * Add product ids array in collection data
	 *
	 * @param  [type] $productArray [description]
	 * @return [type]               [description]
	 */
	private function insertProduct($productArray) {
		$save = $this->sitProductCompFactory->create();
		$save->setData($productArray);
		$save->save();
	}

	/**
	 * Check url key is exist or not
	 *
	 * @param  [type] $entityId [description]
	 * @param  [type] $urlKey   [description]
	 * @return [type]           [description]
	 */
	private function checkUrlKey($entityId, $urlKey) {
		$model = $this->productcompFactory->create()->getCollection()->addFieldToFilter("url_key", ["like" => $urlKey])->addFieldToFilter("entity_id", ["neq" => $entityId])->setOrder("productcompatibility_id", "DESC")->getFirstItem();
		if (!empty($model->getData())) {
			return 0;
		} else {
			return 1;
		}
	}

	/**
	 * Check attribute option value already exist or not
	 *
	 * @param  [type] $optionsArr [description]
	 * @param  [type] $attrCode   [description]
	 * @param  [type] $attrLabel  [description]
	 * @return [type]             [description]
	 */
	private function checkAttrExist($optionsArr, $attrCode, $attrLabel) {

		$attributeInfo = $this->attributeFactory->getCollection()
			->addFieldToFilter('attribute_code', ['eq' => $attrCode])
			->getFirstItem();
		$option = [];
		if ($attributeInfo->getAttributeId()) {
			$option['attribute_id'] = $attributeInfo->getAttributeId();
			$option['value'][$attrLabel][0] = $attrLabel; // For store vise, $option['value'][$attrLabel]['dynamic_store_id']
		}
		if (count($optionsArr) > 0) {
			$optValSave = [];
			foreach ($optionsArr as $key => $value) {
				$optValSave[] = trim($value['value']);
			}
			if (!in_array(trim($attrLabel), $optValSave)) {
				if (!empty($option)) {
					$eavSetup = $this->eavSetupFactory->create();
					$eavSetup->addAttributeOption($option);
				}
			}
		} else {
			if (!empty($option)) {
				$eavSetup = $this->eavSetupFactory->create();
				$eavSetup->addAttributeOption($option);
			}
		}

	}
}
