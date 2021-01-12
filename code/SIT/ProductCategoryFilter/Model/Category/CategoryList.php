<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductCategoryFilter\Model\Category;

use Magento\Catalog\Model\Category as CategoryModel;

class CategoryList implements \Magento\Framework\Option\ArrayInterface {

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
	 */
	private $categoryCollectionFactory;
	/**
	 * [__construct description]
	 * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory [description]
	 */
	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
	) {
		$this->categoryCollectionFactory = $collectionFactory;
	}

	/**
	 * Get list of categories
	 * @return array
	 */
	public function toOptionArray() {
		/**
		 * Get category collection same as visible in product edit page.
		 *
		 * Reference from: Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Categories getCategoriesTree()
		 */
		$collection = $this->categoryCollectionFactory->create();
		$collection->addAttributeToSelect(['name', 'is_active', 'parent_id']);
		$categoryById = [
			CategoryModel::TREE_ROOT_ID => [
				'value' => CategoryModel::TREE_ROOT_ID,
				'optgroup' => null,
			],
		];
		foreach ($collection as $category) {
			foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
				if (!isset($categoryById[$categoryId])) {
					$categoryById[$categoryId] = ['value' => $categoryId];
				}
			}

			$categoryById[$category->getId()]['is_active'] = $category->getIsActive();
			$categoryById[$category->getId()]['label'] = $category->getName();
			$categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
		}

		return $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
	}
}
