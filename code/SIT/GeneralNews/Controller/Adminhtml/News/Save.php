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
use SIT\GeneralNews\Model\NewsFactory;

class Save extends Action {
	/**
	 * @var NewsFactory
	 */
	protected $newsFactory;

	/**
	 * @param Context     $context     [description]
	 * @param NewsFactory $newsFactory [description]
	 */
	public function __construct(
		Context $context,
		NewsFactory $newsFactory
	) {
		$this->newsFactory = $newsFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_GeneralNews::news');
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
			$newsData = $this->newsFactory->create();
			$newsData->setStoreId($storeId);
			$params['store'] = $storeId;
			if (empty($data['entity_id'])) {
				$data['entity_id'] = null;
			} else {
				$newsData->load($data['entity_id']);
				$params['entity_id'] = $data['entity_id'];
			}
			$newsData->addData($data);

			$this->_eventManager->dispatch(
				'sit_generalnews_news_prepare_save',
				['object' => $this->newsFactory, 'request' => $this->getRequest()]
			);

			try {
				$newsData->save();
				$this->messageManager->addSuccessMessage(__('You saved this record.'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$params['entity_id'] = $newsData->getId();
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

}
