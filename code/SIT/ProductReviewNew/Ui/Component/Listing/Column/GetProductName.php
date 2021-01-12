<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

/**
 * Return product name in grid
 */
namespace SIT\ProductReviewNew\Ui\Component\Listing\Column;

class GetProductName extends \Magento\Ui\Component\Listing\Columns\Column {

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $_productFactory;

	/**
	 * @var \SIT\ProductReviewNew\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $sitProCollFactory;

	public function __construct(
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\SIT\ProductReviewNew\Model\ResourceModel\Product\CollectionFactory $sitProCollFactory,
		\Magento\Framework\View\Element\UiComponent\ContextInterface $context,
		\Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = []
	) {

		$this->_productFactory = $productFactory;
		$this->sitProCollFactory = $sitProCollFactory;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as $key => $value) {
				$name = '';
				$sitProData = $this->sitProCollFactory->create()->addFieldToSelect('product_id')->addFieldToFilter('productreview_id', ['eq' => $value['entity_id']]);
				foreach ($sitProData as $keyPro => $product) {
					$product = $this->_productFactory->create()->load($product->getProductId());
					$name .= $product->getName() . ", ";
				}
				$dataSource['data']['items'][$key]['product_id'] = rtrim($name, ', ');
			}
		}
		return $dataSource;
	}
}
