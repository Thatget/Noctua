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
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductReviewNew\Model\ProductReview;
use SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory;

class MassStatus extends Action {
	/**
	 * @var Filter
	 */
	protected $filter;

	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @var ProductReview
	 */
	protected $review;

	/**
	 * [__construct description]
	 * @param Context           $context           [description]
	 * @param Filter            $filter            [description]
	 * @param CollectionFactory $collectionFactory [description]
	 * @param ProductReview     $review            [description]
	 */
	public function __construct(
		Context $context,
		Filter $filter,
		CollectionFactory $collectionFactory,
		ProductReview $review
	) {
		$this->filter = $filter;
		$this->collectionFactory = $collectionFactory;
		$this->review = $review;
		parent::__construct($context);
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute() {
		$reviewData = $this->collectionFactory->create();
		$reviewId = [];
		foreach ($reviewData as $value) {
			$reviewId[] = $value['entity_id'];
		}
		$parameterData = $this->getRequest()->getParams('status');
		$selectedAppsid = $this->getRequest()->getParams('status');
		if (array_key_exists("selected", $parameterData)) {
			$selectedAppsid = $parameterData['selected'];
		}
		if (array_key_exists("excluded", $parameterData)) {
			if ($parameterData['excluded'] == 'false') {
				$selectedAppsid = $reviewId;
			} else {
				$selectedAppsid = array_diff($reviewId, $parameterData['excluded']);
			}
		}
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
		$status = 0;
		$model = [];
		foreach ($collection as $item) {
			$this->setStatus($item->getEntityId(), $this->getRequest()->getParam('status'));
			$status++;
		}
		$this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $status));
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		return $resultRedirect->setPath('*/*/');
	}

	/**
	 * setStatus of selected review
	 * @param [type] $id    [description]
	 * @param [type] $Param [description]
	 */
	public function setStatus($entity_id, $Param) {
		$item = $this->review->load($entity_id);
		$item->setStatus($Param)->save();
		return;
	}
}
