<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Controller\News;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class View extends Action {
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * [__construct description]
	 * @param Context                                    $context           [description]
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory [description]
	 */
	public function __construct(
		Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		parent::__construct($context);
	}

	/**
	 * Custom router controller redirect
	 */
	public function execute() {
		$this->_view->loadLayout();
		$resultPage = $this->resultPageFactory->create();
		$block = $this->_view->getLayout()->createBlock('SIT\GeneralNews\Block\GeneralNews\GeneralNews')->getNewsDetails();
		$resultPage->getConfig()->getTitle()->set($block->getNewsTitle());
		$this->_view->renderLayout();
	}
}
