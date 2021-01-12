<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductCategoryFilter\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Category extends \Magento\Ui\Component\Listing\Columns\Column {

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	private $productFactory;

	/**
	 * @var \Magento\Catalog\Model\CategoryFactory
	 */
	private $categoryFactory;

	/**
	 * [__construct description]
	 * @param ContextInterface                       $context            [description]
	 * @param UiComponentFactory                     $uiComponentFactory [description]
	 * @param array                                  $components         [description]
	 * @param array                                  $data               [description]
	 * @param \Magento\Catalog\Model\ProductFactory  $productFactory     [description]
	 * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory    [description]
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = [],
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory
	) {
		parent::__construct($context, $uiComponentFactory, $components, $data);
		$this->productFactory = $productFactory;
		$this->categoryFactory = $categoryFactory;
	}

	/**
	 * Prepare date for category column
	 * @param  array  $dataSource [description]
	 * @return array
	 */
	public function prepareDataSource(array $dataSource) {
		$fieldName = $this->getData('name');
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as &$item) {
				$productId = $item['entity_id'];
				$product = $this->getProduct($productId);
				$categoryIds = $product->getCategoryIds();
				$categories = [];
				if (count($categoryIds)) {
					foreach ($categoryIds as $categoryId) {
						$categoryData = $this->getCategory($categoryId);
						$categories[] = $categoryData->getName();
					}
				}
				$item[$fieldName] = implode(',', $categories);
			}
		}
		return $dataSource;
	}

	/**
	 * Get product by id
	 * @param  int
	 * @return \Magento\Catalog\Model\ProductFactory
	 */
	public function getProduct($productId) {
		return $this->productFactory->create()->unsetData()->load($productId);
	}

	/**
	 * Get category by id
	 * @param  int
	 * @return \Magento\Catalog\Model\CategoryFactory
	 */
	public function getCategory($categoryId) {
		return $this->categoryFactory->create()->unsetData()->load($categoryId);
	}
}
