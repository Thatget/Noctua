<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [10-05-2019]
 */

namespace Emipro\Newsletterpopup\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class State extends Action
{

    /**
     * PageFactory
     */
    protected $pageFactory;

    /**
     * Country
     */
    protected $country;

    /**
     * [__construct description]
     * @param Context                          $context     [description]
     * @param PageFactory                      $pageFactory [description]
     * @param \Magento\Directory\Model\Country $country     [description]
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Directory\Model\Country $country
    ) {
        $this->pageFactory = $pageFactory;
        $this->country = $country;
        parent::__construct($context);
    }

    public function execute()
    {
        $countryCode = $this->getRequest()->getParam('country');
        $regionCollection = $this->country->loadByCode($countryCode)->getRegions();
        $states = [];
        foreach ($regionCollection as $region) {
            $states[] = ['name' => $region['default_name']];
        }
        echo json_encode($states);
    }
}
