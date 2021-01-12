<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [18-06-2019]
 */
namespace SIT\ProductReviewNew\Controller\Adminhtml\ProductReview;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;

class Reviewgrids extends \Magento\Backend\App\Action
{
    /**
     * [__construct description]
     * @param Context       $context           [description]
     * @param PageFactory   $resultPageFactory [description]
     * @param Rawfactory    $resultRawFactory  [description]
     * @param LayoutFactory $layoutFactory     [description]
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Rawfactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * [execute description]
     * @return html
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'SIT\ProductReviewNew\Block\Adminhtml\Tab\Reviewgrid',
                'sit.productreviewnew.tab.reviewgrid'
            )->toHtml()
        );
    }
}
