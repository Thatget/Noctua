<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-3-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mediarocks\ProSlider\Model\SliderFactory;

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
	 * @var SliderFactory
	 */
	protected $sliderFactory;

	/**
	 * @param Context     $context           [description]
	 * @param PageFactory $resultPageFactory [description]
	 * @param Registry    $registry          [description]
	 * @param SliderFactory $sliderFactory       [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Registry $registry,
		SliderFactory $sliderFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		$this->sliderFactory = $sliderFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Mediarocks_ProSlider::slider');
	}

	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$sliderInstance = $this->sliderFactory->create();
		if ($id) {
			$sliderInstance->load($id);
			if (!$sliderInstance->getId()) {
				$this->messageManager->addErrorMessage(__('This record no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/');
			}
		}
		$this->_coreRegistry->register('entity_id', $id);
		$data = $this->_session->getFormData(true);
		if (!empty($data)) {
			$sliderInstance->addData($data);
		}
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Mediarocks_ProSlider::mediarocks');
		$resultPage->getConfig()->getTitle()->prepend(__('Edit Slider'));
		return $resultPage;
	}
}
