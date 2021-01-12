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
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\ProductReviewNew\Model\ProductReviewFactory;

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
	 * @var ProductReviewFactory
	 */
	protected $_reviewFactory;

	/**
	 * [__construct description]
	 * @param Context              $context           [description]
	 * @param PageFactory          $resultPageFactory [description]
	 * @param Registry             $registry          [description]
	 * @param ProductReviewFactory $reviewFactory     [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Registry $registry,
		ProductReviewFactory $reviewFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		$this->_reviewFactory = $reviewFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductReviewNew::menu');
	}

	/**
	 * Edit
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$reviewInstance = $this->_reviewFactory->create();

		if ($id) {
			$reviewInstance->load($id);
			if (!$reviewInstance->getId()) {
				$this->messageManager->addErrorMessage(__('This record no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$data = $this->_session->getFormData(true);
		if (!empty($data)) {
			$reviewInstance->addData($data);
		}

		$this->_coreRegistry->register('entity_id', $id);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('SIT_ProductReviewNew::productreview');
		$resultPage->getConfig()->getTitle()->prepend(__("Edit Product Review 'Review'"));

		return $resultPage;
	}
}
