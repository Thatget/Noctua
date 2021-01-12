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
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory;

class Edit extends Action {
	/**
	 * Core registry
	 *
	 * @var Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var CollectionFactory
	 */
	protected $generalnewsFactory;

	/**
	 * @param Context           $context            [description]
	 * @param PageFactory       $resultPageFactory  [description]
	 * @param Registry          $registry           [description]
	 * @param CollectionFactory $generalnewsFactory [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Registry $registry,
		CollectionFactory $generalnewsFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		$this->generalnewsFactory = $generalnewsFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_GeneralNews::news');
	}

	/**
	 * Edit
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$storeId = $this->getRequest()->getParam('store', 0);
		$newsData = $this->generalnewsFactory->create();

		if ($id) {
			$newsInstance = $newsData->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
			if (!$newsInstance->getEntityId()) {
				$this->messageManager->addErrorMessage(__('This record no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$data = $this->_session->getFormData(true);
		if (!empty($data)) {
			$newsData->addData($data);
		}

		$this->_coreRegistry->register('entity_id', $id);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('SIT_GeneralNews::news');
		$resultPage->getConfig()->getTitle()->prepend(__('Edit General News ' . '\'' . $newsInstance->getNewsTitle() . '\''));

		return $resultPage;
	}
}
