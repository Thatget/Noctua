<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [28-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Unused;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Unuseddata extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
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
        $this->coreSession = $coreSession;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $compType = $this->getRequest()->getParam('comptype');
        if ($compType == '') {
            $compType = $this->coreSession->getProCompType();
        }
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Unused ' . $compType . ' Compatibilities'));
        return $resultPage;
    }
}
