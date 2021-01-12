<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [1-3-2019]
 */

namespace SIT\ProductTechNew\Block\ProductTechnology;

use SIT\ProductTechNew\Model\ResourceModel\ProductTechnology\CollectionFactory;

class ProductTechnologies extends \Magento\Framework\View\Element\Template
{

    /**
     * sit product table name for join product name
     */
    const SIT_PRODUCTTECH_PRODUCT_TABLE = 'sit_producttechnew_producttechnology_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * @var \SIT\ProductTechNew\Model\ResourceModel\ProductTechnology\CollectionFactory;
     */
    protected $productTechnologyFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\ProductReviewNew\Model\ProductFactory
     */
    protected $sitProductFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context $context                  [description]
     * @param CollectionFactory                                $productTechnologyFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager             [description]
     * @param \Magento\Catalog\Model\ProductFactory            $productFactory           [description]
     * @param \SIT\ProductTechNew\Model\ProductFactory         $sitProductFactory        [description]
     * @param array                                            $data                     [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $productTechnologyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \SIT\ProductTechNew\Model\ProductFactory $sitProductFactory,
        array $data = []
    ) {
        $this->productTechnologyFactory = $productTechnologyFactory;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->sitProductFactory = $sitProductFactory;
        parent::__construct($context, $data);
    }

    /**
     * List of product technology on product page
     *
     * @return \SIT\ProductTechNew\Model\ResourceModel\ProductTechnology\CollectionFactory;
     */
    public function getProductTechnologyDetails($currentProID)
    {
        $technologies = $this->productTechnologyFactory->create()
            ->setStoreId($this->getCurrentStoreId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1]);

        $technologies->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTTECH_PRODUCT_TABLE],
            'e.entity_id = t2.producttechnology_id',
            ['t2.position', 't2.product_id']
        );
        $technologies->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.entity_id SEPARATOR \',\'),\',\',4) as pid'
        );
        $technologies->getSelect()->group('e.entity_id');
        $technologies->getSelect()->where('t2.product_id = ? ', $currentProID);
        $technologies->getSelect()->order(['position ASC', 'created_at DESC']);

        return $technologies;
    }

    /**
     * Return Current Store ID
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    protected function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
