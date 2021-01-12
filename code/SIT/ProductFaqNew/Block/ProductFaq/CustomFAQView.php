<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Block\ProductFaq;

class CustomFAQView extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                    $context           [description]
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager      [description]
     * @param \Magento\Catalog\Model\CategoryFactory                              $categoryFactory   [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory [description]
     * @param array                                                               $data              [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->productFaqFactory = $productFaqFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get category id by name
     *
     * @param  string $name
     * @return int
     */
    public function getCategoryId($name)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $category = $this->categoryFactory->create()->setStoreId($storeId)->getCollection()->addAttributeToSelect('name')->addAttributeToFilter('name', ['like' => $name])->getFirstItem();
        return $category->getEntityId();
    }

    /**
     * Get FAQ Collection by category ID
     *
     * @param  int $categoryId
     * @return \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    public function getFaqCollection($categoryId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->productFaqFactory->create()->setStoreId($storeId)->addAttributeToSelect(["faq_multi_category", "faq_que", "faq_ans"])->addAttributeToFilter('faq_multi_category', ['like' => '%' . $categoryId . '%']);
        return $collection;
    }

    /**
     * Get FAQ by id and product id
     *
     * @param  int $faq_view
     * @param  int $productID
     * @return \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    public function getFAQCustomForProduct($faq_view, $productID)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $faqs = $this->productFaqFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1])
            ->addAttributeToFilter('entity_id', ['eq' => $faq_view]);

        /**
         * START : Join Query For Add Product Name : RH
         */
        /*$faqs->getSelect()->joinLeft(
        ['t2' => self::SIT_PRODUCTFAQ_PRODUCT_TABLE],
        'e.entity_id = t2.productfaq_id',
        't2.product_id'
        );
        $faqs->getSelect()->joinLeft(
        ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
        't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
        'substring_index(GROUP_CONCAT(DISTINCT pv.entity_id SEPARATOR \',\'),\',\',4) as pid'
        );
        $faqs->getSelect()->group('e.entity_id');
        $faqs->getSelect()->where('t2.product_id = ? ', $productID);
        $faqs->getSelect()->order('position ASC');*/
        return $faqs;
    }

    /**
     * Get FAQ by id for category page
     *
     * @param  int $faq_view
     * @return \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    public function getFAQCustomForCategory($faq_view)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $faqs = $this->productFaqFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1])
            ->addAttributeToFilter('entity_id', ['eq' => $faq_view]);
        return $faqs;
    }

}
