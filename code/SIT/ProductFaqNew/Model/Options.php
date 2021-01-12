<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Model;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Options tree for "Categories" field
 */
class Options implements OptionSourceInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $categoriesTree;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryFaqFactory;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory $categoryFaqFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->_categoryFaqFactory = $categoryFaqFactory;
    }

    /**
     * [toOptionArray description]
     * @return [type] [description]
     */
    public function toOptionArray()
    {
        /**
         * Changes by MD : 19-9-2019
         */
        if ($this->categoriesTree === null) {
            $matchingNamesCollection = $this->categoryCollectionFactory->create();

            $matchingNamesCollection->addAttributeToSelect('path')
                ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::ROOT_CATEGORY_ID]);

            $shownCategoriesIds = [];

            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($matchingNamesCollection as $category) {
                foreach (explode('/', $category->getPath()) as $parentId) {
                    $shownCategoriesIds[$parentId] = 1;
                }
            }

            $catFaqData = $this->_categoryFaqFactory->create();
            $catFaqData->addFieldToSelect('*')->addFieldToFilter('id', ['in' => array_keys($shownCategoriesIds)])->setOrder('position', 'ASC');

            $categoryById = [];
            foreach ($catFaqData as $category) {
                foreach ([$category->getId(), $category->getParentCatName()] as $categoryId) {
                    if (!isset($categoryById[$categoryId])) {
                        $categoryById[$categoryId] = ['value' => $categoryId];
                    }
                }
                $categoryById[$category->getId()]['label'] = $category->getCatName();
                $categoryById[$category->getParentCatName()]['optgroup'][] = &$categoryById[$category->getId()];
            }
            if (!empty($categoryById)) {
                $this->categoriesTree = $categoryById[CategoryModel::ROOT_CATEGORY_ID]['optgroup'];
            } else {
                $this->categoriesTree = [['value' => 0, 'label' => 'Default Category']];
            }
        }
        return $this->categoriesTree;
    }
}
