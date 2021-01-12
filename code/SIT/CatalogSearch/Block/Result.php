<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SIT\CatalogSearch\Block;

use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

/**
 * Product search result block
 *
 * @api
 * @since 100.0.2
 */
class Result extends Template
{
    /**
     * sit_productcompatibility_productcompatibility_product
     */
    const SIT_PRODUCTCOMPATIBILITY_PRODUCTCOMPATIBILITY_PRODUCT = 'sit_productcompatibility_productcompatibility_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * Catalog Product collection
     *
     * @var Collection
     */
    protected $productCollection;

    /**
     * Catalog search data
     *
     * @var Data
     */
    protected $catalogSearchData;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * @var \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    protected $reviewCollFactory;

    /**
     * @var \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory
     */
    protected $newsCollFactory;

    /**
     * @var \Onibi\StoreLocator\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var  \Magento\Cms\Helper\Page
     */
    protected $pageUrl;

    /**
     * @var   \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var  \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory
     */
    protected $generalTechnologyFactory;

    /**
     * @var \Magento\Variable\Model\VariableFactory
     */
    protected $varFactory;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $blockColFactory;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * @var \SIT\ProductCompatibility\Model\ProductCompatibilityFactory
     */
    protected $productCompFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productloader;

    /**
     * [__construct description]
     * @param Context                                                                             $context                   [description]
     * @param LayerResolver                                                                       $layerResolver             [description]
     * @param Data                                                                                $catalogSearchData         [description]
     * @param QueryFactory                                                                        $queryFactory              [description]
     * @param \Magento\Store\Model\StoreManagerInterface                                          $storeManager              [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory                 $productFaqFactory         [description]
     * @param \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory           $reviewCollFactory         [description]
     * @param \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory                         $newsCollFactory           [description]
     * @param \Onibi\StoreLocator\Model\StoreFactory                                              $storeFactory              [description]
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory                    $countryCollectionFactory  [description]
     * @param \Magento\Directory\Model\CountryFactory                                             $countryFactory            [description]
     * @param \Magento\Cms\Model\PageFactory                                                      $pageFactory               [description]
     * @param \Magento\Cms\Helper\Page                                                            $pageUrl                   [description]
     * @param \Magento\Cms\Model\Template\FilterProvider                                          $filterProvider            [description]
     * @param \Magento\Cms\Model\BlockFactory                                                     $blockFactory              [description]
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory                     $categoryCollectionFactory [description]
     * @param \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory $generalTechnologyFactory  [description]
     * @param \Magento\Variable\Model\VariableFactory                                             $varFactory                [description]
     * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory                            $blockColFactory           [description]
     * @param ProductCompHelper                                                                   $prodCompHelper            [description]
     * @param \SIT\ProductCompatibility\Model\ProductCompatibilityFactory                         $productCompFactory        [description]
     * @param array                                                                               $data                      [description]
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Data $catalogSearchData,
        QueryFactory $queryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewCollFactory,
        \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory $newsCollFactory,
        \Onibi\StoreLocator\Model\StoreFactory $storeFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Helper\Page $pageUrl,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory $generalTechnologyFactory,
        \Magento\Variable\Model\VariableFactory $varFactory,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory,
        ProductCompHelper $prodCompHelper,
        \SIT\ProductCompatibility\Model\ProductCompatibilityFactory $productCompFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
        $this->catalogSearchData = $catalogSearchData;
        $this->queryFactory = $queryFactory;
        $this->_storeManager = $storeManager;
        $this->productFaqFactory = $productFaqFactory;
        $this->reviewCollFactory = $reviewCollFactory;
        $this->newsCollFactory = $newsCollFactory;
        $this->storeFactory = $storeFactory;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->countryFactory = $countryFactory;
        $this->pageFactory = $pageFactory;
        $this->pageUrl = $pageUrl;
        $this->filterProvider = $filterProvider;
        $this->blockFactory = $blockFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->generalTechnologyFactory = $generalTechnologyFactory;
        $this->varFactory = $varFactory;
        $this->blockColFactory = $blockColFactory;
        $this->prodCompHelper = $prodCompHelper;
        $this->productCompFactory = $productCompFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->productloader = $productloader;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Custom Collection[MD][START][15-05-2019]
     */
    public function getFaqCollection()
    {
        $storeId = $this->getStoreId();
        $faqCollection = $this->productFaqFactory->create()->setStoreId($storeId)->addAttributeToSelect('*')->addAttributeToFilter('status', 1)->addAttributeToSort('faq_que', 'asc');
        return $faqCollection;
    }
    public function getFaqFilter($faqs, $text, $isQuoted)
    {
        if ($isQuoted == true) {
            $faqFilter = $faqs->addAttributeToFilter([
                ['attribute' => 'faq_que', 'like' => '%' . trim($text, '"') . '%'],
                ['attribute' => 'faq_ans', 'like' => '%' . trim($text, '"') . '%']
            ]);
            return $faqFilter;
        } else {
            $faqFilter = $faqs->addAttributeToFilter($text);
            return $faqFilter;
        }
    }
    public function getReviewCollection()
    {
        $storeId = $this->getStoreId();
        $reviewCollection = $this->reviewCollFactory->create()->setStoreId($storeId)->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1);
        return $reviewCollection;
    }
    public function getReviewFilter($reviews, $text, $isQuoted)
    {
        if ($isQuoted == true) {
            $reviewFilter = $reviews->addAttributeToFilter([
                ['attribute' => 'review_title', 'like' => '%' . trim($text, '"') . '%'],
                ['attribute' => 'review_website', 'like' => '%' . trim($text, '"') . '%'],
                ['attribute' => 'review_short_desc', 'like' => '%' . trim($text, '"') . '%'],
                ['attribute' => 'review_desc', 'like' => '%' . trim($text, '"') . '%'],
                ['attribute' => 'review_link', 'like' => '%' . trim($text, '"') . '%'],
            ]);
            return $reviewFilter;
        } else {
            $reviewFilter = $reviews->addAttributeToFilter($text);
            return $reviewFilter;
        }
    }
    public function getNewsCollection()
    {
        $storeId = $this->getStoreId();
        $newsCollection = $this->newsCollFactory->create()->setStoreId($storeId)->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToSort('created_at', 'desc');
        return $newsCollection;
    }
    public function getNewsFilter($news, $text, $isQuoted)
    {
        if ($isQuoted == true) {
            $newsFilter = $news->addAttributeToFilter([
                ['attribute' => 'news_title', 'like' => '%' . trim($text, '"') . '%'],
                ['attribute' => 'news_shortdesc', 'like' => '%' . trim($text, '"') . '%'],
                ['attribute' => 'news_desc', 'like' => '%' . trim($text, '"') . '%'],
            ]);
            return $newsFilter;
        } else {
            $newsFilter = $news->addAttributeToFilter($text);
            return $newsFilter;
        }
    }
    public function getStoreCollection()
    {
        $storeCollection = $this->storeFactory->create()->getCollection()->addFieldToFilter('status', 1)
            ->addFieldToSelect('*')->setOrder('name', 'asc');
        return $storeCollection;
    }
    public function getCountryCollection()
    {
        return $this->countryCollectionFactory->create()->loadByStore();
    }
    public function getCountryname($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
    public function getStoreFilter($stores, $text, $countryId)
    {
        if ($countryId != '') {
            $storeFilter = $stores->addFieldToFilter(
                ['name', 'city', 'address', 'country_id'],
                [
                    ['like' => '%' . trim($text, '"') . '%'],
                    ['like' => '%' . trim($text, '"') . '%'],
                    ['like' => '%' . trim($text, '"') . '%'],
                    ['like' => '%' . trim($countryId) . '%'],
                ]
            );
            return $storeFilter;
        } else {
            $storeFilter = $stores->addFieldToFilter(
                ['name', 'city', 'address'],
                [
                    ['like' => '%' . trim($text, '"') . '%'],
                    ['like' => '%' . trim($text, '"') . '%'],
                    ['like' => '%' . trim($text, '"') . '%'],
                ]
            );
            return $storeFilter;
        }
    }
    public function getCompCollection($compType)
    {
        $compCollection = $this->productCompFactory->create()->getCollection()->addAttributeToSelect('comp_manufacture')
            ->addAttributeToSelect('comp_socket')
            ->addAttributeToSelect('comp_model')
            ->addAttributeToSort('comp_socket', 'ASC')
            ->addAttributeToSort('comp_manufacture', 'ASC')
            ->addAttributeToSort('entity_id', 'ASC');

        $modelAllOption = $this->getCompTypeCollection();

        //Mainboard
        if ($compType == ProductCompHelper::COMP_MAINBOARD) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        //Ram
        if ($compType == ProductCompHelper::COMP_RAM) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        //Case
        if ($compType == ProductCompHelper::COMP_CASE) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        //CPU
        if ($compType == ProductCompHelper::COMP_CPU) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        return $compCollection;
    }
    public function getCompTypeCollection()
    {
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        return $modelAllOption;
    }
    public function getCompTypeId($modelAllOption, $compType)
    {
        $optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($compType));
        return $optionId;
    }
    public function getCompAttrValue($compAttr)
    {
        if ($compAttr == 'COMP_MANUFACTURE') {
            $manufInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
        }
        if ($compAttr == 'COMP_MODEL') {
            $manufInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
        }
        if ($compAttr == 'COMP_SOCKET') {
            $manufInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
        }
        $manufId = $manufInfo->getAttributeId();
        $manufAllOption = $this->prodCompHelper->getAttributeOptionAll($manufId);

        return $manufAllOption;
    }
    public function getManufLinksMainboard($mid, $mval, $modlcomparr, $type)
    {
        $compatibilities = $this->getManufLinksMainboardCollection($mid);
        $baseUrl = $this->getBaseUrl();
        $manlinksarr = [];
        foreach ($compatibilities as $comp) {
            $manlinkstr = '<a style="color:#616161;" href="' . $baseUrl . $type . '/' . $comp->getUrlKey() . '">' . array_search($mid, $modlcomparr) . '</a>';
            $manlinksarr[$comp->getData('comp_manufacture')] = $manlinkstr;
        }

        return $manlinksarr;
    }
    public function getManufLinks($mid, $mval, $modlcomparr, $type)
    {
        $compatibilities = $this->getManufLinksMainboardCollection($mid);
        $baseUrl = $this->getBaseUrl();
        $manlinksarr = [];
        foreach ($compatibilities as $comp) {
            $manlinkstr = '<a style="color:#616161;" href="' . $baseUrl . $type . '/' . $comp->getUrlKey() . '">' . array_search($mid, $modlcomparr) . '</a>';
            $manlinksarr[$comp->getData('comp_manufacture')] = $manlinkstr;
        }

        return $manlinksarr;
    }
    public function getManufLinksMainboardCollection($mid)
    {
        $storeId = $this->getStoreId();
        $compatibilities = $this->productCompFactory->create()->getCollection()
            ->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToSelect('url_key')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('comp_model', $mid);

        return $compatibilities;
    }
    public function getProCollection()
    {
        $storeId = $this->getStoreId();
        $collection = $this->productCollectionFactory->create();
        $collection->addStoreFilter($storeId, false);
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->addAttributeToSelect('name');
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        return $collection;
    }
    public function getCompProduct($productsArray)
    {
        $products = $this->productloader->create()->load($productsArray);
        return $products;
    }
    public function getCompsCollect($productId, $compType)
    {
        $storeId = $this->getStoreId();
        $compCollection = $this->productCompFactory->create()->getCollection()->setStoreId($storeId)->addAttributeToFilter('status', 1);
        /**
         * START : Join Query For Add Product Name : MD
         */
        $compCollection->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTCOMPATIBILITY_PRODUCTCOMPATIBILITY_PRODUCT],
            'e.entity_id = t2.productcompatibility_id',
            't2.product_id'
        );
        $compCollection->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.value SEPARATOR \',\'),\',\',4) as pname'
        );
        $compCollection->getSelect()->group('e.entity_id');
        /**
         * END : Join Query For Add Product Name : MD
         */
        $compCollection->getSelect()->where('t2.product_id = ? ', $productId);

        $modelAllOption = $this->getCompTypeCollection();
        //Mainboard
        if ($compType == ProductCompHelper::COMP_MAINBOARD) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        //Ram
        if ($compType == ProductCompHelper::COMP_RAM) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        //Case
        if ($compType == ProductCompHelper::COMP_CASE) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        //CPU
        if ($compType == ProductCompHelper::COMP_CPU) {
            $optionId = $this->getCompTypeId($modelAllOption, $compType);
            $compCollection->addAttributeToFilter('comp_type', $optionId);
        }
        return $compCollection;
    }

    public function getCmsPageCollection()
    {
        $storeId = $this->getStoreId();
        $cmsPageColl = $this->pageFactory->create()->getCollection()->addStoreFilter($storeId, false)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('title', ['nlike' => '404%']);
        return $cmsPageColl;
    }
    public function getCmsPageFilter($cmspages, $text)
    {
        $cmsPageFilter = $cmspages->addFieldToFilter(
            ['title', 'content', 'meta_description', 'identifier'],
            [
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
            ]
        );
        return $cmsPageFilter;
    }
    public function getPageContentFilter($pageContent)
    {
        return $this->filterProvider->getBlockFilter()->filter($pageContent);
    }
    public function getTcmsPageCollection($text)
    {
        $storeId = $this->getStoreId();
        $tcmsPageColl = $this->pageFactory->create()->getCollection()
            ->addStoreFilter($storeId, false)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('title', ['nlike' => '404%'])
            ->addFieldToFilter('identifier', ['like' => '%' . trim($text, '"') . '%']);
        return $tcmsPageColl;
    }
    public function getTcmsPageFilter($tcmspages, $text)
    {
        $tcmsPagesFilter = $tcmspages->addFieldToFilter(
            ['title', 'content', 'meta_description', 'identifier'],
            [
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
            ]
        );
        return $tcmsPagesFilter;
    }
    public function getCategoryCollection()
    {
        $storeId = $this->getStoreId();
        $categoryCollection = $this->categoryCollectionFactory->create()->setStoreId($storeId, false)
            ->addIsActiveFilter();
        return $categoryCollection;
    }
    public function getCategoryFilter($categories, $text, $isQuoted)
    {
        if ($isQuoted == true) {
            $categoryFilter = $categories->addAttributeToFilter(
                [
                    ['attribute' => 'name', ['like' => '%' . trim($text, '"') . '%']],
                    ['attribute' => 'description', ['like' => '%' . trim($text, '"') . '%']],
                ]
            );
            return $categoryFilter;
        } else {
            $categoryFilter = $categories->addAttributeToFilter($text);
            return $categoryFilter;
        }
    }
    public function getGeneralTechCollection()
    {
        $storeId = $this->getStoreId();
        $generalTechColl = $this->generalTechnologyFactory->create()->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1);
        return $generalTechColl;
    }
    public function getGeneralTechFilter($_generaltechnologies, $text, $isQuoted)
    {
        if ($isQuoted == true) {
            $generalTechFilter = $_generaltechnologies->addAttributeToFilter(
                [
                    ['attribute' => 'gen_technology_title', 'like' => '%' . trim($text, '"') . '%'],
                    ['attribute' => 'gen_technology_shortdesc', 'like' => '%' . trim($text, '"') . '%'],
                    ['attribute' => 'gen_technology_desc', 'like' => '%' . trim($text, '"') . '%'],
                    ['attribute' => 'url_key', 'like' => '%' . trim($text, '"') . '%'],
                ]
            );
            return $generalTechFilter;
        } else {
            $generalTechFilter = $_generaltechnologies->addAttributeToFilter($text);
            return $generalTechFilter;
        }
    }
    public function getVariableValue($varCode)
    {
        $storeId = $this->getStoreId();
        $varValue = $this->varFactory->create()->setStoreId($storeId)->loadByCode($varCode);
        return $varValue;
    }
    public function getBlockColllection()
    {
        $storeId = $this->getStoreId();
        $blockCollection = $this->blockColFactory->create()->addStoreFilter($storeId, false)->addFieldToFilter('is_active', 1)->addFieldToFilter('title', ['nlike' => '404%'])->addFieldToFilter('block_url_page', ['neq' => '']);
        return $blockCollection;
    }
    public function getBlockFilterData($cmsblocks, $text)
    {
        $cmsBlockFilter = $cmsblocks->addFieldToFilter(
            ['title', 'content', 'identifier'],
            [
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
                ['like' => '%' . trim($text, '"') . '%'],
            ]
        );
        return $cmsBlockFilter;
    }
    public function getStoreData($code, $storeId)
    {
        return $this->scopeConfig->getValue($code, $storeId);
    }
    public function getPageUrl($pageId)
    {
        return $this->pageUrl->getPageUrl($pageId);
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    /**
     * Custom Collection[MD][END][15-05-2019]
     */

    /**
     * Retrieve query model object
     *
     * @return \Magento\Search\Model\Query
     */
    protected function _getQuery()
    {
        return $this->queryFactory->get();
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $title = $this->getSearchQueryText();
        $this->pageConfig->getTitle()->set($title);
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl(),
                ]
            )->addCrumb(
                'search',
                ['label' => $title, 'title' => $title]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
    }

    /**
     * Retrieve search list toolbar block
     *
     * @return ListProduct
     */
    public function getListBlock()
    {
        return $this->getChildBlock('search_result_list');
    }

    /**
     * Set search available list orders
     *
     * @return $this
     */
    public function setListOrders()
    {
        $category = $this->catalogLayer->getCurrentCategory();
        /* @var $category \Magento\Catalog\Model\Category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders['relevance'] = __('Relevance');

        $this->getListBlock()->setAvailableOrders(
            $availableOrders
        )->setDefaultDirection(
            'desc'
        )->setDefaultSortBy(
            'relevance'
        );

        return $this;
    }

    /**
     * Set available view mode
     *
     * @return $this
     */
    public function setListModes()
    {
        $test = $this->getListBlock();
        $test->setModes(['grid' => __('Grid'), 'list' => __('List')]);
        return $this;
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Collection
     */
    protected function _getProductCollection()
    {
        if (null === $this->productCollection) {
            $this->productCollection = $this->getListBlock()->getLoadedProductCollection();
        }

        return $this->productCollection;
    }

    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSearchQueryText()
    {
        return __("Search results for: '%1'", $this->catalogSearchData->getEscapedQueryText());
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->_getQuery()->saveNumResults($size);
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getNoResultText()
    {
        if ($this->catalogSearchData->isMinQueryLength()) {
            return __('Minimum Search query length is %1', $this->_getQuery()->getMinQueryLength());
        }
        return $this->_getData('no_result_text');
    }

    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
        return $this->catalogSearchData->getNoteMessages();
    }
}
