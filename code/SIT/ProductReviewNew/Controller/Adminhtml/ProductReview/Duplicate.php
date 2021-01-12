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
use SIT\ProductReviewNew\Model\ProductReviewFactory;

class Duplicate extends Action {
	/**
	 * @var \SIT\ProductReviewNew\Model\ProductReviewFactory
	 */
	protected $reviewFactory;

	/**
	 * @var ProductReviewFactory
	 */
	protected $product;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $date;

	/**
	 * @param Action\Context                              $context       [description]
	 * @param ProductReviewFactory                        $reviewFactory [description]
	 * @param \SIT\ProductReviewNew\Model\ProductFactory  $product       [description]
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime $date          [description]
	 */
	public function __construct(
		Action\Context $context,
		ProductReviewFactory $reviewFactory,
		\SIT\ProductReviewNew\Model\ProductFactory $product,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
	) {
		parent::__construct($context);
		$this->reviewFactory = $reviewFactory;
		$this->product = $product;
		$this->date = $date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$editDuplicate = $this->getRequest()->getParam('edit_duplicate');
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($id) {
			try {
				$reviewModel = $this->reviewFactory->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
				$products = $this->product->create()->getCollection()->addFieldToFilter("productreview_id", ["eq" => $reviewModel->getEntityId()]);

				$duplicateData = $reviewModel->getData();
				$duplicateData["created_at"] = $this->date->gmtDate();
				array_shift($duplicateData);
				$this->createDuplicate($duplicateData, $products->getData());
				$this->messageManager->addSuccess(__('Duplicate generated'));
				$lastId = $this->reviewFactory->create()->getCollection()->getLastItem();
				if ($editDuplicate == 1) {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				} else {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				}
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
			}
		}
		$this->messageManager->addError(__('Record does not exist'));
		return $resultRedirect->setPath('*/*/');
	}

	/**
	 * Create duplicate from main table
	 *
	 * @param  [type] $data     [description]
	 * @param  [type] $products [description]
	 * @return [type]           [description]
	 */
	private function createDuplicate($data, $products) {
		$reviewModel = $this->reviewFactory->create();
		$reviewModel->setData($data);
		$reviewModel->save();
		$this->insertProductIds($reviewModel->getEntityId(), $products);
	}

	/**
	 * Loop of product ids
	 *
	 * @param  [type] $reviewId [description]
	 * @param  [type] $products   [description]
	 * @return [type]             [description]
	 */
	private function insertProductIds($reviewId, $products) {
		try {
			foreach ($products as $key => $value) {
				$fields = ["productreview_id" => $reviewId, "product_id" => $value["product_id"], "position" => $value["position"]];
				$this->insertProduct($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}

	/**
	 * insert selected products
	 *
	 * @param  [type] $productArray [description]
	 * @return [type]               [description]
	 */
	private function insertProduct($productArray) {
		$productData = $this->product->create();
		$productData->setData($productArray);
		$productData->save();
	}
}
