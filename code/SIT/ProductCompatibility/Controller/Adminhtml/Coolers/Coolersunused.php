<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [23-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Coolers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Coolersunused extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * [__construct description]
     * @param Context     $context           [description]
     * @param PageFactory $resultPageFactory [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductCompatibility::case');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SIT_ProductCompatibility::case');
        $resultPage->getConfig()->getTitle()->prepend(__('Unused Compatibility Case / Coolers'));
        return $resultPage;
    }
}
