<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Fans;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;

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
	 * @var ProductCompatibilityFactory
	 */
	protected $productcompFactory;

	/**
	 * [__construct description]
	 * @param Context                     $context            [description]
	 * @param PageFactory                 $resultPageFactory  [description]
	 * @param Registry                    $registry           [description]
	 * @param ProductCompatibilityFactory $productcompFactory [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Registry $registry,
		ProductCompatibilityFactory $productcompFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		$this->productcompFactory = $productcompFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductCompatibility::fans');
	}

	/**
	 * Edit
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$productcompInstance = $this->productcompFactory->create();

		if ($id) {
			$productcompInstance->load($id);
			if (!$productcompInstance->getId()) {
				$this->messageManager->addErrorMessage(__('This record no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$data = $this->_session->getFormData(true);
		if (!empty($data)) {
			$productcompInstance->addData($data);
		}

		$this->_coreRegistry->register('entity_id', $id);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('SIT_ProductCompatibility::productcompatibility');
		$resultPage->getConfig()->getTitle()->prepend(__('Edit Fans'));

		return $resultPage;
	}
}
