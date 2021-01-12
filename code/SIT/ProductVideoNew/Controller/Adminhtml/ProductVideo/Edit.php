<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Controller\Adminhtml\ProductVideo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\ProductVideoNew\Model\ProductVideoFactory;

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
     * @var ProductVideoFactory
     */
    protected $_productVideoNewFactory;

    /**
     * [__construct description]
     * @param Context                                                                $context              [description]
     * @param PageFactory                                                            $resultPageFactory    [description]
     * @param Registry                                                               $registry             [description]
     * @param \SIT\ProductVideoNew\Model\ResourceModel\ProductVideo\CollectionFactory $productVideoNewFactory [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        \SIT\ProductVideoNew\Model\ResourceModel\ProductVideo\CollectionFactory $productVideoNewFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_productVideoNewFactory = $productVideoNewFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductVideoNew::productvideo');
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
        $productvideoData = $this->_productVideoNewFactory->create();
        if ($id) {
            $productvideo = $productvideoData->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
            if (!$productvideo->getEntityId()) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $productvideoData->addData($data);
        }

        $this->_coreRegistry->register('entity_id', $id);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SIT_ProductVideoNew::productvideo');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Product Video ' . '\'' . $productvideo->getVideoTitle() . '\''));

        return $resultPage;
    }
}
