<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\ProductReview;

use Magento\Framework\Controller\ResultFactory;

class Koproductview extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\App\Action\Context      $context           [description]
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory [description]
     * @param \Magento\Framework\Json\Helper\Data        $jsonHelper        [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Get review collection from block
     * @return [type] [description]
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $paramArray = [];
        /**
         * This if condition is used here because, if direct url execute then, it will return error to decode content.
         */
        if ($this->getRequest()->getContent()) {
            $paramArray = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
            if (array_key_exists('product_id', $paramArray)) {
                $productId = $paramArray['product_id'];
                $resultPage = $this->resultPageFactory->create();
                $block = $resultPage->getLayout()->createBlock('SIT\ProductReviewNew\Block\ProductReview\View');
                $collection = $block->getProductPageReviewData($productId);
                $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
                $response->setHeader('Content-type', 'application/json');
                $response->setContents($this->jsonHelper->jsonEncode($collection));
                return $response;
            }
        }
    }
}
