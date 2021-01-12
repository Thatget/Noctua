<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\ProductFaq;

class CategoryFaq extends \Magento\Framework\App\Action\Action {

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\App\Action\Context      $context           [description]
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

	/**
	 * Execute view action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$this->_view->loadLayout();
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set('FAQ');
		$resultPage->getConfig()->setPageLayout('1column');
		$this->_view->renderLayout();
	}
}
