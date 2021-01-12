<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-06-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Fans;

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
     * [__construct description]
     * @param PageFactory                 $resultPageFactory  [description]
     * @param Registry                    $registry           [description]
     * @param ProductCompatibilityFactory $productcompFactory [description]
     * @param Context                     $context            [description]
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Registry $registry,
        ProductCompatibilityFactory $productcompFactory,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->productcompFactory = $productcompFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductCompatibility::casefans');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('selected');
        $this->_coreRegistry->register('entity_id', $id);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Case / Fans Compatibility MassActions'));
        return $resultPage;
    }
}
