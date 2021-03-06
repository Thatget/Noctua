<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\CPU;

class Productgrid extends \Magento\Catalog\Controller\Adminhtml\Product {

	/**
	 * @var \Magento\Framework\View\Result\LayoutFactory
	 */
	protected $resultLayoutFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\App\Action\Context                   $context             [description]
	 * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder      [description]
	 * @param \Magento\Framework\View\Result\LayoutFactory          $resultLayoutFactory [description]
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
		\Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
	) {
		parent::__construct($context, $productBuilder);
		$this->resultLayoutFactory = $resultLayoutFactory;
	}

	/**
	 * @return \Magento\Framework\View\Result\LayoutFactory
	 */
	public function execute() {
		$this->productBuilder->build($this->getRequest());
		$resultLayout = $this->resultLayoutFactory->create();
		$resultLayout->getLayout()->getBlock('sit.productcompatibility.cpu.tab.productgrid')
			->setProducts($this->getRequest()->getPost('products', null));
		return $resultLayout;
	}
}
