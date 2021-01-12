<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Controller\Adminhtml\Newsletter;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Emipro\Newslettergroup\Model\Newsletter
     */
    protected $jobs;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context        $context           [description]
     * @param \Emipro\Newslettergroup\Model\Newsletter   $jobs              [description]
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory [description]
     * @param \Magento\Framework\Registry                $coreRegistry      [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Emipro\Newslettergroup\Model\Newsletter $jobs,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->jobs = $jobs;
        $this->resultPageFactory = $resultPageFactory;

        $this->_coreRegistry = $coreRegistry;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Emipro_Newslettergroup::grid');
        return $resultPage;
    }
    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $model = $this->jobs;

        if ($id) {
            $model->load($id);

            if (!$id) {
                $this->messageManager->addError(__('This template record no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/index');
            }
        }

        $data = $this->_session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? 'Edit Item ' . "'" . $model->getTitle() . "'" : __('Add Item'));
        return $resultPage;

    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emipro_Newslettergroup::addnew');
    }
}
