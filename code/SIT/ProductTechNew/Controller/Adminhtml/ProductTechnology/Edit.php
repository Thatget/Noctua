<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Controller\Adminhtml\ProductTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\ProductTechNew\Model\ResourceModel\ProductTechnology\CollectionFactory;

class Edit extends Action
{
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
     * @var ProductTechnologyFactory
     */
    protected $productTechnologyFactory;

    /**
     * [__construct description]
     * @param Context           $context                  [description]
     * @param PageFactory       $resultPageFactory        [description]
     * @param Registry          $registry                 [description]
     * @param CollectionFactory $productTechnologyFactory [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        CollectionFactory $productTechnologyFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_productTechnologyFactory = $productTechnologyFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductTechNew::producttechnology');
    }

    /**
     * Edit
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $storeId = (int) $this->getRequest()->getParam('store');
        $productTechInstance = $this->_productTechnologyFactory->create();

        if ($id) {
            $productTechnew = $productTechInstance->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
            if (!$productTechnew->getEntityId()) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $productTechInstance->addData($data);
        }

        $this->_coreRegistry->register('entity_id', $id);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SIT_ProductTechNew::producttechnology');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Product Technology ' . '\'' . $productTechnew->getTechnologyTitle() . '\''));

        return $resultPage;
    }
}
