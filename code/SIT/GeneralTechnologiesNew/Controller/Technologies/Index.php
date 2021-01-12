<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Controller\Technologies;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action {
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\View\Page\Config
	 */
	protected $pageConfig;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * [__construct description]
	 * @param Context                                          $context           [description]
	 * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory [description]
	 * @param \Magento\Framework\View\Page\Config              $pageConfig        [description]
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory [description]
	 */
	public function __construct(
		Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->pageConfig = $pageConfig;
		$this->resultJsonFactory = $resultJsonFactory;
		parent::__construct($context);
	}

	/**
	 * Custom router controller redirect
	 */
	public function execute() {
		$this->_view->loadLayout();
		$this->pageConfig->getTitle()->set(__('Technologies'));
		$this->_view->renderLayout();
	}
}
