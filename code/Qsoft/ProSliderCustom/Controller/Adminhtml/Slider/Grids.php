<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProSliderCustom\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;

class Grids extends \Mediarocks\ProSlider\Controller\Adminhtml\Slider\Grids
{
    protected $resultPageFactory;
    protected $resultRawFactory;
    protected $layoutFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Rawfactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context, $resultPageFactory, $resultRawFactory, $layoutFactory);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * [execute description]
     * @return html
     */
    public function execute() {
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Qsoft\ProSliderCustom\Block\Adminhtml\Tab\Slidegrid',
                'qsoft.proslidercustom.tab.productgrid'
            )->toHtml()
        );
    }
}