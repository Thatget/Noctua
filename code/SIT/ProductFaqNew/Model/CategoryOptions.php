<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Model;

class CategoryOptions implements \Magento\Framework\Data\OptionSourceInterface {

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
	 */
	protected $categoryCollectionFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory [description]
	 */
	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
	) {
		$this->categoryCollectionFactory = $categoryCollectionFactory;
	}

	/**
	 * get list of category for ui form
	 * @return [type] [description]
	 */
	public function toOptionArray() {
		$categories = $this->categoryCollectionFactory->create()->addAttributeToSelect(['entity_id', 'name']);//->addAttributeToFilter("level", ["in" => [0, 1, 2]]);

		$options = [];
		foreach ($categories as $rows) {
			$options[] = [
				'label' => $rows->getName(),
				'value' => $rows->getEntityId(),
			];
		}
		return $options;
	}
}
