<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\Adminhtml\ProductReview;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Backend\App\Action {
	/**
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute() {
		$resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$resultPage->getConfig()->getTitle()->prepend(__('Add Product Review'));
		return $resultPage;
	}
}
