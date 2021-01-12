<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */

namespace Mediarocks\ProSlider\Model\Option;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Framework\Option\ArrayInterface;

class ProductSKU implements ArrayInterface {

	/**
	 * @var CollectionFactory
	 */
	protected $productFactory;

	/**
	 * @param CollectionFactory $productFactory [description]
	 */
	public function __construct(
		CollectionFactory $productFactory
	) {
		$this->productFactory = $productFactory;
	}

	/**
	 * ui-form select option product sku
	 * @return array
	 */
	public function toOptionArray() {
		$sku = [];
		$collection = $this->productFactory->create()->addStoreFilter(0);
		foreach ($collection as $product) {
			$sku[] = ['value' => $product->getSku(), 'label' => $product->getSku()];
		}
		return $sku;
	}
}