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
namespace SIT\ProductCompatibility\Ui\Component\Listing\Column;

class GetProductName extends \Magento\Ui\Component\Listing\Columns\Column {

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $_productFactory;

	/**
	 * @var \SIT\ProductCompatibility\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $sitProCollFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Catalog\Model\ProductFactory                                   $productFactory     [description]
	 * @param \SIT\ProductCompatibility\Model\ResourceModel\Product\CollectionFactory $sitProCollFactory  [description]
	 * @param \Magento\Framework\View\Element\UiComponent\ContextInterface            $context            [description]
	 * @param \Magento\Framework\View\Element\UiComponentFactory                      $uiComponentFactory [description]
	 * @param array                                                                   $components         [description]
	 * @param array                                                                   $data               [description]
	 */
	public function __construct(
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\SIT\ProductCompatibility\Model\ResourceModel\Product\CollectionFactory $sitProCollFactory,
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
				$sitProData = $this->sitProCollFactory->create()->addFieldToSelect('product_id')->addFieldToFilter('productcompatibility_id', ['eq' => $value['entity_id']]);
				foreach ($sitProData as $keyPro => $product) {
					$product = $this->_productFactory->create()->load($product->getProductId());
					$name .= $product->getName() . ", ";
				}
				$filtered_name = explode(',', $name);
				if (count($filtered_name) > 5) {
					$dataSource['data']['items'][$key]['product_name'] = $filtered_name[0] . "," . $filtered_name[1] . "," . $filtered_name[2] . "," . $filtered_name[3] . "," . $filtered_name[4];
				} else {
					$dataSource['data']['items'][$key]['product_name'] = rtrim($name, ', ');
				}
			}
		}
		return $dataSource;
	}
}
