<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;

class Grids extends \Magento\Backend\App\Action {
	/**
	 * @param Context       $context           [description]
	 * @param PageFactory   $resultPageFactory [description]
	 * @param Rawfactory    $resultRawFactory  [description]
	 * @param LayoutFactory $layoutFactory     [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Rawfactory $resultRawFactory,
		LayoutFactory $layoutFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->resultRawFactory = $resultRawFactory;
		$this->layoutFactory = $layoutFactory;
	}

	/**
	 * [execute description]
	 * @return html
	 */
	public function execute() {
		$resultRaw = $this->resultRawFactory->create();
		return $resultRaw->setContents(
			$this->layoutFactory->create()->createBlock(
				'Mediarocks\ProSlider\Block\Adminhtml\Tab\Slidegrid',
				'mediarocks.proslider.tab.slidegrid'
			)->toHtml()
		);
	}
}
