<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

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
	 * @var \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory
	 */
	protected $_categoryFaqFactory;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Registry $registry,
		\SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory $categoryFaqFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		$this->_categoryFaqFactory = $categoryFaqFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductFaqNew::create_category');
	}

	/**
	 * Edit
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('id');
		$catFaqData = $this->_categoryFaqFactory->create();
		if ($id) {
			$catFaqObj = $catFaqData->addFieldToSelect('*')->addFieldToFilter("id", ["eq" => $id])->getFirstItem();

			if (!$catFaqObj->getId()) {
				$this->messageManager->addErrorMessage(__('This record no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/');
			}
		}

		$data = $this->_session->getFormData(true);
		if (!empty($data)) {
			$catFaqData->addData($data);
		}

		$this->_coreRegistry->register('entity_id', $id);
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('SIT_ProductFaqNew::create_category');
		$resultPage->getConfig()->getTitle()->prepend(__('Edit FAQ Category ' . '\'' . $catFaqObj->getCatName() . '\''));
		//$resultPage->getConfig()->getTitle()->prepend(__('Edit FAQ Category'));
		return $resultPage;
	}
}
