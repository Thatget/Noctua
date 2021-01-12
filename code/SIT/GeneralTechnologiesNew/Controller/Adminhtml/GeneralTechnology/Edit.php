<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Controller\Adminhtml\GeneralTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory;

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
	 * @var GeneralTechnologyFactory
	 */
	protected $generalTechnologyFactory;

	/**
	 * [__construct description]
	 * @param Context           $context                  [description]
	 * @param PageFactory       $resultPageFactory        [description]
	 * @param Registry          $registry                 [description]
	 * @param CollectionFactory $generalTechnologyFactory [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Registry $registry,
		CollectionFactory $generalTechnologyFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		$this->_generalTechnologyFactory = $generalTechnologyFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_GeneralTechnologiesNew::generaltechnology');
	}

	/**
	 * Edit
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$storeId = (int) $this->getRequest()->getParam('store');
		$generalTechnologiesInstance = $this->_generalTechnologyFactory->create();

		if ($id) {
			$generalTechnew = $generalTechnologiesInstance->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
			if (!$generalTechnew->getEntityId()) {
				$this->messageManager->addErrorMessage(__('This record no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$data = $this->_session->getFormData(true);
		if (!empty($data)) {
			$generalTechnologiesInstance->addData($data);
		}

		$this->_coreRegistry->register('entity_id', $id);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('SIT_GeneralTechnologiesNew::generaltechnology');
		$resultPage->getConfig()->getTitle()->prepend(__('Edit General Technology ' . '\'' . $generalTechnew->getGenTechnologyTitle() . '\''));

		return $resultPage;
	}
}
