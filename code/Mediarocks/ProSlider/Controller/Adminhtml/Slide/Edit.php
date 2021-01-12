<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-3-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mediarocks\ProSlider\Model\SlideFactory;

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
	 * @var SlideFactory
	 */
	protected $slideFactory;

	/**
	 * [__construct description]
	 * @param Context     $context           [description]
	 * @param PageFactory $resultPageFactory [description]
	 * @param Registry    $registry          [description]
	 * @param SlideFactory $slideFactory       [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Registry $registry,
		SlideFactory $slideFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		$this->slideFactory = $slideFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Mediarocks_ProSlider::slide');
	}

	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$slideInstance = $this->slideFactory->create();

		if ($id) {
			$slideInstance->load($id);
			if (!$slideInstance->getId()) {
				$this->messageManager->addErrorMessage(__('This record no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$data = $this->_session->getFormData(true);
		if (!empty($data)) {
			$slideInstance->addData($data);
		}

		$this->_coreRegistry->register('entity_id', $id);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Mediarocks_ProSlider::mediarocks');
		$resultPage->getConfig()->getTitle()->prepend(__('Slide Edit'));

		return $resultPage;
	}
}
