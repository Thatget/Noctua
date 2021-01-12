<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Model\Config\Source;

class ProductNameList implements \Magento\Framework\Option\ArrayInterface {

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory [description]
	 */
	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
	) {
		$this->collectionFactory = $collectionFactory;
	}

	/**
	 * Return options array
	 *
	 * @return array
	 */
	public function toOptionArray() {
		$options = [];
		$productCollection = $this->collectionFactory->create()->addAttributeToSelect('*');
		foreach ($productCollection as $product) {
			$options[] = ['label' => $product->getName(), 'value' => $product->getId()];
		}
		return $options;
	}
}