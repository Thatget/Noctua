<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Controller\Technologies;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class View extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \SIT\GeneralTechnologiesNew\Model\GeneralTechnologyFactory
     */
    protected $generalTechFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * [__construct description]
     * @param Context                                                                             $context            [description]
     * @param \Magento\Framework\View\Result\PageFactory                                          $resultPageFactory  [description]
     * @param \Magento\Framework\Controller\Result\JsonFactory                                    $resultJsonFactory  [description]
     * @param \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory $generalTechFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                                          $storeManager       [description]
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory $generalTechFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->generalTechFactory = $generalTechFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Custom router controller redirect
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $resultPage = $this->resultPageFactory->create();
        $tech_id = $this->getRequest()->getParam('tech_id');
        $storeId = $this->storeManager->getStore()->getId();
        $generalTechInstance = $this->generalTechFactory->create()->setStore($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $tech_id])->getFirstItem();
        $resultPage->getConfig()->getTitle()->prepend(__($generalTechInstance->getGenTechnologyTitle()));
        $resultPage->getConfig()->setPageLayout('1column');
        $this->_view->renderLayout();
    }
}
