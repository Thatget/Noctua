<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\TemplateText\Controller\Adminhtml\TemplateText;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory;

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
     * @var TemplateTextFactory
     */
    protected $_templatetextFactory;

    /**
     * [__construct description]
     * @param Context              $context              [description]
     * @param PageFactory          $resultPageFactory    [description]
     * @param Registry             $registry             [description]
     * @param TemplateTextFactory $templatetextFactory [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        CollectionFactory $templatetextFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_templatetextFactory = $templatetextFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_TemplateText::templatetext');
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
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $templatetextData = $this->_templatetextFactory->create();

        if ($id) {
            $templateTextNew = $templatetextData->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
            if (!$templateTextNew->getEntityId()) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $templatetextData->addData($data);
        }

        $this->_coreRegistry->register('entity_id', $id);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SIT_TemplateText::templatetext');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit TemplateText ' . '\'' . $templateTextNew->getTemplateTextTitle() . '\''));

        return $resultPage;
    }
}
