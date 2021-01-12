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
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Newsletter extends Action
{

    /**
     * PageFactory
     */
    protected $pageFactory;

    /**
     * @var Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context                          $context     [description]
     * @param PageFactory                      $pageFactory [description]
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        JsonFactory $jsonFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        $resultPage = $this->pageFactory->create();
        $block = $resultPage->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Magento_Theme::newsletter.phtml')
            ->toHtml();
        $result->setData(['output' => $block]);
        return $result;
    }
}
