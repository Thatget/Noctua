<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh
 */
namespace SIT\ProductFaqNew\Block\ProductFaq;

class ProductFAQView extends \Magento\Framework\View\Element\Template
{
    /**
     * For load category by url key
     */
    const CATEGORY_URL_KEY = 'products';

    /**
     * sit product table name for join product name
     */
    const SIT_PRODUCTFAQ_PRODUCT_TABLE = 'sit_productfaqnew_productfaq_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context                    $context           [description]
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager      [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory [description]
     * @param array                                                               $data              [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->productFaqFactory = $productFaqFactory;
        parent::__construct($context, $data);
    }

    /**
     * Return FAQ List in product view page
     *
     * @param  [type] $productID [description]
     * @return \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    public function getProductFAQData($productID)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $faqs = $this->productFaqFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1]);
        /**
         * START : Join Query For Add Product Name : RH
         */
        $faqs->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTFAQ_PRODUCT_TABLE],
            'e.entity_id = t2.productfaq_id',
            ['t2.position', 't2.product_id']
        );
        $faqs->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.entity_id SEPARATOR \',\'),\',\',4) as pid'
        );
        $faqs->getSelect()->group('e.entity_id');
        $faqs->getSelect()->where('t2.product_id = ? ', $productID);
        $faqs->getSelect()->order(['position ASC', 'created_at DESC']);
        /**
         * END : Join Query For Add Product Name : RH
         */
        return $faqs;
    }

    /**
     * Return current url of the page
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getCurrentUrl()
    {
        return $this->storeManager->getStore()->getCurrentUrl();
    }
}
