<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\Adminhtml\ProductReview;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductReviewNew\Model\Product as SitReviewProduct;
use SIT\ProductReviewNew\Model\ProductFactory as SitReviewProductFactory;
use SIT\ProductReviewNew\Model\ProductReviewFactory;

class Save extends Action {
	/**
	 * @var ProductReviewFactory
	 */
	protected $_reviewFactory;

	/**
	 * [__construct description]
	 * @param Context                 $context                 [description]
	 * @param ProductReviewFactory    $reviewFactory           [description]
	 * @param SitReviewProduct        $sitReviewProduct        [description]
	 * @param SitReviewProductFactory $sitReviewProductFactory [description]
	 */
	public function __construct(
		Context $context,
		ProductReviewFactory $reviewFactory,
		SitReviewProduct $sitReviewProduct,
		SitReviewProductFactory $sitReviewProductFactory
	) {
		$this->_reviewFactory = $reviewFactory;
		$this->sitReviewProduct = $sitReviewProduct;
		$this->sitReviewProductFactory = $sitReviewProductFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductReviewNew::menu');
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
			$reviewInstance = $this->_reviewFactory->create();
			$reviewInstance->setStoreId($storeId);
			$params['store'] = $storeId;

			if (empty($data['entity_id'])) {
				$data['entity_id'] = null;
			} else {
				$reviewInstance->load($data['entity_id']);
				$params['entity_id'] = $data['entity_id'];
			}
			$reviewInstance->addData($data);

			$this->_eventManager->dispatch(
				'sit_productreviewnew_productreview_prepare_save',
				['object' => $this->_reviewFactory, 'request' => $this->getRequest()]
			);

			try {
				$reviewInstance->save();

				if (isset($data['productreviewnew_products'])
					&& is_string($data['productreviewnew_products'])) {
					$productsId = (array) json_decode($data['productreviewnew_products']);
					$this->insertProductIds($reviewInstance->getId(), $productsId);
				}
				$this->messageManager->addSuccessMessage(__('You saved this record.'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$params['entity_id'] = $reviewInstance->getId();
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
	private function insertProductIds($reviewId, $productsId) {
		$tempSitReviewProduct = $this->sitReviewProduct;
		$collection = $tempSitReviewProduct->getCollection()->addFieldToFilter("productreview_id", ["eq" => $reviewId]);
		foreach ($collection->getData() as $value) {
			$tempSitReviewProduct->load($value["rel_id"]);
			$tempSitReviewProduct->delete();
			$tempSitReviewProduct->unsetData();
		}
		try {
			/**
			 * All fields value save from associated product and position null if position field will blank
			 */
			foreach ($productsId as $key => $value) {
				if ($value != "" && $value >= 0) {
					if (is_numeric($value)) {
						$fields = ["productreview_id" => $reviewId, "product_id" => $key, "position" => $value];
					} else {
						$fields = ["productreview_id" => $reviewId, "product_id" => $key, "position" => $value];
						$this->messageManager->addWarning('You have entered text value for product position. By default set value 0');
					}
				} else {
					$fields = ["productreview_id" => $reviewId, "product_id" => $key, "position" => null];
				}
				$this->insertProduct($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}
	private function insertProduct($productArray) {
		$reviewModel = $this->sitReviewProductFactory->create();
		$reviewModel->setData($productArray);
		$reviewModel->save();
	}

}
