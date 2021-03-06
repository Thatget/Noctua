<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Qsoft\ContentCustom\Controller\Adminhtml\LinkReplace;

class Grid extends \Magento\Backend\App\Action
{
    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context        $context           [description]
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
