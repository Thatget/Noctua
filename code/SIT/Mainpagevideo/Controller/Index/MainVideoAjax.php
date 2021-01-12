<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Mainpagevideo\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class MainVideoAjax extends Action {

	/**
	 * @var PageFactory
	 */
	protected $pageFactory;

	/**
	 * @var JsonFactory
	 */
	protected $jsonFactory;

	/**
	 * @param Context          $context          [description]
	 * @param PageFactory      $pageFactory      [description]
	 * @param JsonFactory      $jsonFactory      [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $pageFactory,
		JsonFactory $jsonFactory
	) {
		$this->pageFactory = $pageFactory;
		$this->jsonFactory = $jsonFactory;
		parent::__construct($context);
	}

	/**
	 * @return JsonFactory
	 */
	public function execute() {
        $resultPage = $this->pageFactory->create();
		$result = $this->jsonFactory->create();
        $block = $resultPage->getLayout()->createBlock('SIT\Mainpagevideo\Block\Mainpagevideo\Mainpagevideo')->getDataCollectionArray();
		return $result->setData($block);
	}
}
