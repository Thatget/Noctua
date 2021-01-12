<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [02-05-2019]
 */

namespace SIT\WebformApi\Controller\Country;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Price extends Action
{

    /**
     * PageFactory
     */
    protected $pageFactory;

    /**
     * [__construct description]
     * @param Context                            $context     [description]
     * @param PageFactory                        $pageFactory [description]
     * @param \SIT\WebformApi\Model\Countryprice $model       [description]
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \SIT\WebformApi\Model\Countryprice $model
    ) {
        $this->pageFactory = $pageFactory;
        $this->modelCountryprice = $model;
        parent::__construct($context);
    }

    public function execute()
    {
        $country = $this->getRequest()->getParam("country");
        echo $this->modelCountryprice->load($country, "country_id")->getPrice();
    }
}
