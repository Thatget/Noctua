<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */

namespace Mediarocks\ProSlider\Model\Option;

use \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use \Magento\Framework\Option\ArrayInterface;

class CategoryList implements ArrayInterface {

	/**
	 * @var CollectionFactory
	 */
	protected $categoryCollection;

	/**
	 * [__construct description]
	 * @param CollectionFactory $categoryCollection [description]
	 */
	public function __construct(
		CollectionFactory $categoryCollection
	) {
		$this->categoryCollection = $categoryCollection;
	}
	/**
	 * ui-form select category
	 * @return array
	 */
	public function toOptionArray() {
		$collection = $this->categoryCollection->create()->setStoreId(0)->addAttributeToSelect('*')->addAttributeToFilter('is_active', '1')->load();
		$category = array();
		foreach ($collection as $categories) {
			$category[] = ['value' => $categories->getUrlKey(), 'label' => $categories->getName()];
		}
		return $category;
	}
}