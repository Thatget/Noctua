<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-06-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Unused;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;

class MassActions extends \Magento\Backend\App\Action
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
     * @var ProductCompatibilityFactory
     */
    protected $productcompFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * [__construct description]
     * @param Context                                            $context            [description]
     * @param PageFactory                                        $resultPageFactory  [description]
     * @param Registry                                           $registry           [description]
     * @param ProductCompatibilityFactory                        $productcompFactory [description]
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession        [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        ProductCompatibilityFactory $productcompFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->productcompFactory = $productcompFactory;
        $this->coreSession = $coreSession;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductCompatibility::CPU');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('selected');
        $this->_coreRegistry->register('entity_id', $id);
        $compType = $this->getRequest()->getParam('comptype');
        if ($compType == '') {
            $compType = $this->coreSession->getProCompType();
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__($compType . ' Compatibility MassActions'));
        return $resultPage;
    }
}
