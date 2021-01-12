<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slider;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index extends Action {

	/**
	 * @param Context     $context           [description]
	 * @param PageFactory $resultPageFactory [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

	public function execute() {
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Mediarocks_ProSlider::mediarocks');
		$resultPage->getConfig()->getTitle()->prepend((__('Manage Slider')));
		return $resultPage;
	}
}