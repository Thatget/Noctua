<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Unused;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Index extends Action
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterfac
     */
    protected $coreSession;

    /**
     * [__construct description]
     * @param Context                                            $context     [description]
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession [description]
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        parent::__construct($context);
        $this->coreSession = $coreSession;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->coreSession->getProCompType() == 'Case') {
            $resultRedirect->setPath('*/coolers/index/comptype/Case/');
        } else {
            $resultRedirect->setPath('*/' . $this->coreSession->getProCompType() . '/index');
        }
        return $resultRedirect;
    }
}
