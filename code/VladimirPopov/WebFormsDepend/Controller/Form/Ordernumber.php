<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [03-05-2019]
 */

namespace VladimirPopov\WebFormsDepend\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Ordernumber extends Action
{

    /**
     * PageFactory
     */
    protected $pageFactory;

    /**
     * [__construct description]
     * @param Context     $context     [description]
     * @param PageFactory $pageFactory [description]
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $orderNumber = $this->getRequest()->getParam('order_number');
        $checkOrderNr = exec("./sit_sale_order_or_invoice_search " . $orderNumber);
        echo $checkOrderNr;
    }
}
