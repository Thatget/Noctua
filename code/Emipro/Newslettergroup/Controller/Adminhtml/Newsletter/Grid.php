<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Controller\Adminhtml\Newsletter;

use Magento\Framework\Controller\Result\RawFactory;

class Grid extends \Emipro\Newslettergroup\Controller\Adminhtml\Newsletter
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context   $context             [description]
     * @param RawFactory                            $resultRawFactory    [description]
     * @param \Magento\Framework\View\LayoutFactory $resultLayoutFactory [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->resultLayoutFactory->create()->createBlock(
                'Emipro\Newslettergroup\Block\Adminhtml\Assignattachment\Attachgrid',
                'Emipro.Newslettergroup.edit.tab.addSubscribersToGroup'
            )->toHtml()
        );
    }

}
