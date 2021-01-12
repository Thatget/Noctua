<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Category;

use Magento\Framework\Controller\ResultFactory;

class BuyingGuideCpuCoolers extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \SIT\MainAdmin\Helper\Data $mainAdminHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper

    ) {
        $this->mainAdminHelper = $mainAdminHelper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $cmsPageData = $this->mainAdminHelper->getCmsPageData('buying-guide-cpu-coolers');
        $buyingGuideCCData = $this->mainAdminHelper->getCmsFilterContent($cmsPageData->getContent());
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode($buyingGuideCCData));
        return $response;
    }
}
