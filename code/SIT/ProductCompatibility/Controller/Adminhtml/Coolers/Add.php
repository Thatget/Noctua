<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Coolers;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Backend\App\Action {
	/**
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute() {
		$resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$resultPage->getConfig()->getTitle()->prepend(__('Add Coolers Compatibility'));
		return $resultPage;
	}
}
