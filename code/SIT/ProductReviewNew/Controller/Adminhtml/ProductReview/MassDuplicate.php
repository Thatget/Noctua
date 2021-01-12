<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\Adminhtml\ProductFaqNew;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory;

class MassDuplicate extends \Magento\Backend\App\Action {

	/**
	 * [$filter description]
	 * @var [type]
	 */
	protected $filter;

	/**
	 * [$collectionFactory description]
	 * @var [type]
	 */
	protected $collectionFactory;

	/**
	 * [$reviewFactory description]
	 * @var [type]
	 */
	protected $reviewFactory;

	/**
	 * [$product description]
	 * @var [type]
	 */
	protected $product;

	/**
	 * [__construct description]
	 * @param Context                                          $context           [description]
	 * @param Filter                                           $filter            [description]
	 * @param CollectionFactory                                $collectionFactory [description]
	 * @param \SIT\ProductReviewNew\Model\ProductReviewFactory $reviewFactory     [description]
	 * @param \SIT\ProductReviewNew\Model\ProductFactory       $product           [description]
	 */
	public function __construct(
		Context $context,
		Filter $filter,
		CollectionFactory $collectionFactory,
		\SIT\ProductReviewNew\Model\ProductReviewFactory $reviewFactory,
		\SIT\ProductReviewNew\Model\ProductFactory $product
	) {
		$this->filter = $filter;
		$this->collectionFactory = $collectionFactory;
		$this->reviewFactory = $reviewFactory;
		$this->product = $product;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$jobData = $this->collectionFactory->create();

		foreach ($jobData as $value) {
			$templateId[] = $value['entity_id'];
		}
		$parameterData = $this->getRequest()->getParams('entity_id');
		$selectedAppsid = $this->getRequest()->getParams('entity_id');
		if (array_key_exists("selected", $parameterData)) {
			$selectedAppsid = $parameterData['selected'];
		}
		if (array_key_exists("excluded", $parameterData)) {
			if ($parameterData['excluded'] == 'false') {
				$selectedAppsid = $templateId;
			} else {
				$selectedAppsid = array_diff($templateId, $parameterData['excluded']);
			}
		}
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter('entity_id', ['in' => $selectedAppsid])->addFieldToSelect('*');
		$model = [];
		foreach ($collection as $item) {
			$products = $this->product->create()->getCollection()->addFieldToFilter("productreview_id", ["eq" => $item->getEntityId()]);
			$duplicateData = $item->getData();
			array_shift($duplicateData);
			$this->createDuplicate($duplicateData, $products->getData());
			$this->messageManager->addSuccess(__('Duplicate generated'));
		}
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
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
		$model = $this->reviewFactory->create();
		$model->setData($data);
		$newQuestionId = $model->save();
		$this->insertProductIds($model->getEntityId(), $products);
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
			$this->messageManager->addError('Product Id not Save.');
		}
	}

	/**
	 * insert selected products
	 *
	 * @param  [type] $productArray [description]
	 * @return [type]               [description]
	 */
	private function insertProduct($productArray) {
		$product = $this->product->create();
		$product->setData($productArray);
		$product->save();
	}
}
