<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\ProductFaq;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use SIT\MainAdmin\Helper\Data;
use SIT\ProductFaqNew\Block\ProductFaq\ProductFAQView;

class FaqView extends Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ProductFAQView
     */
    protected $productFaqView;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Data
     */
    protected $sitHelper;

    /**
     * @param Context        $context           [description]
     * @param PageFactory    $resultPageFactory [description]
     * @param JsonFactory    $jsonFactory       [description]
     * @param ProductFAQView $productFaqView    [description]
     * @param Data           $sitHelper         [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $jsonFactory,
        ProductFAQView $productFaqView,
        Data $sitHelper
    ) {
        $this->productFaqView = $productFaqView;
        $this->jsonFactory = $jsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->sitHelper = $sitHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $currentProductId = $this->getRequest()->getParam('id');
        $resultPage = $this->resultPageFactory->create();
        $result = $this->jsonFactory->create();
        $collection = $this->productFaqView->getProductFAQData($currentProductId);
        $data = [];
        $faqData = [];
        foreach ($collection as $key => $value) {
            $data['entity_id'] = $value->getId();
            $data['faq_que'] = $value->getFaqQue();
            $data['faq_ans'] = $this->sitHelper->getCmsFilterContent($value->getFaqAns());
            array_push($faqData, $data);
        }

        $result->setData($faqData);
        return $result;
    }
}
