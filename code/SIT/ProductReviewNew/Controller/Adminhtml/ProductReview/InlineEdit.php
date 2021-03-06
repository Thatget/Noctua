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
use Magento\Framework\Controller\Result\JsonFactory;
use SIT\ProductReviewNew\Model\ResourceModel\ProductReview\Collection;

/**
 * Grid inline edit controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends Action {
	/**
	 * @var JsonFactory
	 */
	protected $jsonFactory;

	/**
	 * @var Collection
	 */
	protected $_reviewCollection;

	/**
	 * [__construct description]
	 * @param Context     $context          [description]
	 * @param Collection  $reviewCollection [description]
	 * @param JsonFactory $jsonFactory      [description]
	 */
	public function __construct(
		Context $context,
		Collection $reviewCollection,
		JsonFactory $jsonFactory
	) {
		parent::__construct($context);
		$this->jsonFactory = $jsonFactory;
		$this->_reviewCollection = $reviewCollection;
	}

	/**
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		/** @var \Magento\Framework\Controller\Result\Json $resultJson */
		$resultJson = $this->jsonFactory->create();
		$error = false;
		$messages = [];

		$postItems = $this->getRequest()->getParam('items', []);
		if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
			return $resultJson->setData([
				'messages' => [__('Please correct the data sent.')],
				'error' => true,
			]);
		}

		try {
			$this->_reviewCollection
				->setStoreId($this->getRequest()->getParam('store', 0))
				->addFieldToFilter('entity_id', ['in' => array_keys($postItems)])
				->walk('saveCollection', [$postItems]);
		} catch (\Exception $e) {
			$messages[] = __('There was an error saving the data: ') . $e->getMessage();
			$error = true;
		}

		return $resultJson->setData([
			'messages' => $messages,
			'error' => $error,
		]);
	}
}
