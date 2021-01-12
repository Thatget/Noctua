<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Block\ProductFaq;

class CategoryFAQView extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Catalog\Model\productFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

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
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context                    $context                   [description]
     * @param \Magento\Catalog\Model\ProductFactory                               $productFactory            [description]
     * @param \Magento\Catalog\Model\CategoryFactory                              $categoryFactory           [description]
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager              [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory         [description]
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory     $categoryCollectionFactory [description]
     * @param array                                                               $data                      [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->productFaqFactory = $productFaqFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }
    public function getProductFAQList($category_id, $category_name)
    {
        $categoryInstance = $this->categoryFactory->create()->load($category_id);
        $categoryCollection = $categoryInstance->getProductCollection()->addAttributeToSelect('name')->addAttributeToSort('name', 'ASC');
        $categoryCollection->addUrlRewrite($categoryInstance->getId());
        $categoryArray = [];
        if (count($categoryCollection) > 0) {
            $categoryArray['category_name'] = $category_name;
        }
        foreach ($categoryCollection as $key => $product) {
            $product_id = $product->getId();
            $_product = $this->productFactory->create()->load($product_id);
            $faqs = $this->productFaqFactory->create()
                ->setStoreId($this->getCurrentStoreId())
                ->addAttributeToFilter('status', ['eq' => 1]);
            /**
             * START : Join Query For Add Product Name : RH
             */
            $faqs->getSelect()->joinLeft(
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
            /**
             * END : Join Query For Add Product Name : RH
             */
            if ($faqs->getSize() > 0) {
                $categoryArray['products'][$key]['product_name'] = $_product->getName();
                $categoryArray['products'][$key]['product_url'] = $product->getProductUrl();
            }
        }

        try
        {
            $faqData = $this->productFaqFactory->create()
                ->setStoreId($this->getCurrentStoreId())
                ->addAttributeToSelect('faq_multi_category')
                ->addAttributeToFilter('status', ['eq' => 1])
                ->addAttributeToFilter('faq_multi_category', ['like' => '%' . $category_id . '%']);
            if (count($faqData->getData()) > 0) {
                $faqIds = [];
                foreach ($faqData as $key => $value) {
                    if ($value->getFaqMultiCategory() != '-1') {
                        $faqIds[] = $value->getId();
                    }
                }
                $faqCollection = $this->productFaqFactory->create()
                    ->setStoreId($this->getCurrentStoreId())
                    ->addAttributeToFilter('status', ['eq' => 1])
                    ->addAttributeToFilter('entity_id', ['in' => $faqIds]);
                if (count($faqCollection) > 0) {
                    $cat_old = $this->categoryCollectionFactory->create()
                        ->setStoreId($this->getCurrentStoreId())
                        ->addAttributeToSelect("*")
                        ->addFieldToFilter('entity_id', $category_id);
                    $categoryArray['redirect_url'] = $this->storeManager->getStore()->getBaseUrl() . 'support/faq/general-faqs/category/' . $cat_old->getFirstItem()->getName();
                }
            }
            return $categoryArray;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Return FAQs All product data
     *
     * @return array
     */
    public function getFaqCategoryData()
    {
        $category_load = $this->categoryFactory->create()->loadByAttribute('url_key', self::CATEGORY_URL_KEY);
        $subcategories = $category_load->getChildrenCategories();
        $subCatArr = [];
        foreach ($subcategories as $_subcategory) {
            $categoryData = $this->categoryFactory->create()->load($_subcategory->getId());
            if ($categoryData->getData('faq_list_position')) {
                $subCatArr[$categoryData->getData('faq_list_position')] = $_subcategory;
            }
        }
        ksort($subCatArr);
        $data = [];
        if (count($subCatArr) > 0) {
            foreach ($subCatArr as $key => $_subcat) {
                $data[] = $this->getProductFAQList($_subcat->getId(), $_subcat->getName());
            }
        }
        return $data;
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

    /**
     * Return Base Url
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
}
