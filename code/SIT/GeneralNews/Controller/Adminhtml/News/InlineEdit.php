<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Controller\Adminhtml\News;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use SIT\GeneralNews\Model\ResourceModel\News\Collection;

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
	protected $newsCollection;

	/**
	 * @param Context     $context        [description]
	 * @param Collection  $newsCollection [description]
	 * @param JsonFactory $jsonFactory    [description]
	 */
	public function __construct(
		Context $context,
		Collection $newsCollection,
		JsonFactory $jsonFactory
	) {
		parent::__construct($context);
		$this->jsonFactory = $jsonFactory;
		$this->newsCollection = $newsCollection;
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
			$this->newsCollection
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
