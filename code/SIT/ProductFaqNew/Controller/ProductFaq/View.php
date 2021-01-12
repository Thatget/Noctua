<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\ProductFaq;

class View extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context      $context           [description]
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \SIT\ProductFaqNew\Block\Route\ProductFaq $faqBlock,
        \SIT\MainAdmin\Helper\Data $helperData

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->faqBlock = $faqBlock;
        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $faq_id = $this->getRequest()->getParam('id');
        $productFaq = $this->faqBlock->getCustomRouteFAQ($faq_id);

        $description = $this->helperData->getCmsFilterContent($productFaq->getFaqAns());
        $desc = explode('. ', $description);
        $descData = '';
        if (isset($desc[0])) {
            $descData = $desc[0] . '. ';
        }
        if (isset($desc[1])) {
            $descData .= $desc[1] . '.';
        }
        $this->_view->loadLayout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('FAQ: ' . $productFaq->getFaqQue()));
        $resultPage->getConfig()->setDescription(__($descData));
        $resultPage->getConfig()->setPageLayout('1column');
        $this->_view->renderLayout();
    }
}
