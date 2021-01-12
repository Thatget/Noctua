<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Block\ProductReview;

use Magento\Framework\View\Element\Template\Context;

class ListReview extends \Magento\Framework\View\Element\Template
{
    /**
     * sit product table name for join product name
     */
    const SIT_PRODUCTREVIEW_PRODUCT_TABLE = 'sit_productreviewnew_productreview_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * For load category by url key
     */
    const CATEGORY_URL_KEY = 'products';

    /**
     * @var \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    protected $reviewcollFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \SIT\MainAdmin\Helper\Data
     */
    protected $sitHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \SIT\ProductReviewNew\Model\ProductFactory
     */
    protected $sitProductFactory;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context                          $context           [description]
     * @param \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                                $storeManager      [description]
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                      $timezone          [description]
     * @param \Magento\Catalog\Model\ProductFactory                                     $productFactory    [description]
     * @param \SIT\MainAdmin\Helper\Data                                                $sitHelper         [description]
     * @param \Magento\Catalog\Model\CategoryFactory                                    $categoryFactory   [description]
     * @param \Magento\Framework\Json\Helper\Data                                       $jsonHelper        [description]
     * @param \SIT\ProductReviewNew\Model\ProductFactory                                $sitProductFactory [description]
     * @param array                                                                     $data              [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \SIT\MainAdmin\Helper\Data $sitHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \SIT\ProductReviewNew\Model\ProductFactory $sitProductFactory,
        array $data = []
    ) {
        $this->reviewcollFactory = $reviewcollFactory;
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        $this->productFactory = $productFactory;
        $this->sitHelper = $sitHelper;
        $this->categoryFactory = $categoryFactory;
        $this->jsonHelper = $jsonHelper;
        $this->sitProductFactory = $sitProductFactory;
        parent::__construct($context, $data);
    }

    /**
     * List of  review for home page
     *
     * @return \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    public function getListReviewData()
    {
        $currentStore = $this->storeManager->getStore()->getId();
        $reviewData = $this->reviewcollFactory->create();
        $reviewColl = $reviewData->setStoreId($currentStore)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1]);
        /**
         * START : Join Query For Add Product Name : RH
         */
        $reviewColl->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTREVIEW_PRODUCT_TABLE],
            'e.entity_id = t2.productreview_id',
            't2.product_id'
        );
        $reviewColl->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.value SEPARATOR \', \'),\',\',4) as pname'
        );
        $reviewColl->getSelect()->group('e.entity_id');
        /**
         * END : Join Query For Add Product Name : RH
         */
        $reviewColl->setOrder('created_at', 'desc');
        $reviewColl->setOrder('entity_id', 'desc');
        return $reviewColl;
    }

    /**
     * Change date format for review
     *
     * @return date
     */
    public function changeDateFormat($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('d.m.Y');
    }

    /**
     * List of award for awards category
     *
     * @return \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    public function getListAwardData()
    {
        /**
         * Not need to optimize code. It will return wrong sort order
         */
        $currentStore = $this->storeManager->getStore()->getId();
        $reviewData = $this->reviewcollFactory->create();
        $reviewColl = $reviewData->setStoreId($currentStore)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1])->addAttributeToFilter('review_position', ['gt' => 0]);

        /**
         * START : Join Query For Add Product Name : RH
         */
        $reviewColl->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTREVIEW_PRODUCT_TABLE],
            'e.entity_id = t2.productreview_id',
            't2.product_id'
        );
        $reviewColl->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.entity_id SEPARATOR \',\'),\',\',4) as pid'
        );
        $reviewColl->getSelect()->group('e.entity_id');
        $reviewColl->setOrder('created_at', 'desc');
        $reviewColl->setOrder('review_position', 'desc');
        /**
         * END : Join Query For Add Product Name : RH
         */
        $awardCollection = [];
        foreach ($reviewColl as $key => $value) {
            $productIdArr = explode(',', $value['pid']);
            foreach ($productIdArr as $keyPro => $valuePro) {
                $cat_ids = [];
                $flag = false;
                $_product = $this->productFactory->create()->load($valuePro);
                $cat_ids = $_product->getCategoryIds();
                foreach ($cat_ids as $cat) {
                    if ($cat == 16) {
                        $flag = true;
                    }
                }
            }
            if ($flag == false) {
                if ($value->getReviewImage()) {
                    $prolist = '';
                    $proId = '';
                    foreach ($productIdArr as $product) {
                        $_product = $this->productFactory->create()->load($product);
                        $proId = $_product->getId();
                        $prolist = $_product->getName();
                        break;
                    }
                }
                $awardCollection[$key]['product_id'] = $proId;
                $awardCollection[$key]['product_name'] = $prolist;
                $awardCollection[$key]['review_website'] = $value->getReviewWebsite();
                $awardCollection[$key]['review_website_link'] = $value->getReviewLink();
                $awardCollection[$key]['review_image'] = $this->sitHelper->getImage('productreview/image', $value->getReviewImage());
            }
        }
        return $awardCollection;
    }

    /**
     * List of award in product page
     *
     * @param  int $proId
     * @return array
     */
    public function getProductAwardImage($proId)
    {
        /**
         * Not need to optimize code. It will return wrong sort order
         */
        $currentStore = $this->storeManager->getStore()->getId();
        $reviewData = $this->reviewcollFactory->create();
        $reviewColl = $reviewData->setStoreId($currentStore)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1]);

        /**
         * START : Join Query For Add Product Name : RH
         */
        $reviewColl->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTREVIEW_PRODUCT_TABLE],
            'e.entity_id = t2.productreview_id',
            't2.product_id'
        );
        $reviewColl->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.entity_id SEPARATOR \',\'),\',\',4) as pid'
        );
        $reviewColl->getSelect()->group('e.entity_id');
        $reviewColl->getSelect()->where('t2.product_id = ? ', $proId);
        $reviewColl->setOrder('product_review_priority', 'desc');
        $reviewColl->setOrder('created_at', 'desc');
        /**
         * END : Join Query For Add Product Name : RH
         */
        $awardCollection = [];

        foreach ($reviewColl as $key => $value) {
            // $productIdArr = explode(',', $value['pid']);
            // foreach ($productIdArr as $keyPro => $valuePro) {
            //     $cat_ids = [];
            //     $flag = false;
            //     $_product = $this->productFactory->create()->load($valuePro);
            //     $cat_ids = $_product->getCategoryIds();
            //     foreach ($cat_ids as $cat) {
            //         if ($cat == 16) {
            //             $flag = true;
            //         }
            //     }
            // }
            // if ($flag == false) {
            //     if ($value->getReviewImage()) {
            //         $prolist = '';
            //         $proId = '';
            //         foreach ($productIdArr as $product) {
            //             $_product = $this->productFactory->create()->load($product);
            //             $proId = $_product->getId();
            //             break;
            //         }
            //     }
            // $awardCollection[$key]['product_id'] = $proId;
            $awardCollection[$key]['product_id'] = $value['product_id'];
            $awardCollection[$key]['review_image'] = $this->sitHelper->getImage('productreview/image', $value->getReviewImage());
            // }
        }
        return $awardCollection;
    }
    /**
     * List of award in product page
     *
     * @return \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    public function getListAwardImageData($proId)
    {
        $awardList = $this->getProductAwardImage($proId);
        $awardArr = [];
        foreach ($awardList as $key => $value) {
            if ($proId == $value['product_id']) {
                $awardArr[$key]['review_image'] = $value['review_image'];
            }
        }
        $awardArr['collection'] = array_slice($awardArr, 0, 10);
        $awardArr['size'] = count($awardList);
        return $awardArr;
    }

    /**
     * Total number of review in current product
     *
     * @return \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    public function getListReviewCountData($currentProID)
    {
        $reviews = $this->reviewcollFactory->create()
            ->setStoreId($this->getCurrentStoreId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1]);
        /**
         * START : Join Query For Add Product Name : RH
         */
        $reviews->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTREVIEW_PRODUCT_TABLE],
            'e.entity_id = t2.productreview_id',
            't2.product_id'
        );
        $reviews->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.value SEPARATOR \', \'),\',\',4) as pname'
        );
        $reviews->getSelect()->group('e.entity_id');
        $reviews->getSelect()->where('t2.product_id = ? ', $currentProID);
        /**
         * END : Join Query For Add Product Name : RH
         */
        return count($reviews);
    }

    /**
     * Get Base Url
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get Current Product Url
     */
    public function getProductUrl($proId)
    {
        $product = $this->productFactory->create()->load($proId);
        return $product->getProductUrl();
    }

    /**
     * Get Current Product Url for Awards review
     */
    public function getAWProductUrl()
    {
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    /**
     * Code changed by: Pp
     * Return product name list for review page dropdown
     *
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getProductNameList()
    {
        $currentStore = $this->storeManager->getStore()->getId();
        $reviewData = $this->reviewcollFactory->create();
        $reviewColl = $reviewData->setStoreId($currentStore)->addAttributeToSelect('entity_id')->addAttributeToFilter('status', ['eq' => 1]);

        /**
         * Join product ids with review ids for get only those products which contains reviews
         */
        $productIds = [];
        $reviewColl->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTREVIEW_PRODUCT_TABLE],
            'e.entity_id = t2.productreview_id',
            ['product_id' => 'GROUP_CONCAT(t2.product_id)']
        );
        $reviewColl->getSelect()->group('e.entity_id');
        foreach ($reviewColl as $key => $value) {
            $productIds[] = $value['product_id'];
        }
        $productIds = implode(",", $productIds);
        $productIds = array_unique(explode(",", $productIds));

        $multi_category_load = $this->categoryFactory->create()->getCollection();
        $_subcategories = [];
        foreach ($multi_category_load as $key => $category_load) {
            $subcategories = $category_load->getChildrenCategories();
            foreach ($subcategories as $_subcategory) {
                $_subcategory = $this->categoryFactory->create()->load($_subcategory->getId());
                if ($_subcategory->getData('faq_list_position')) {
                    $_subcategories[$_subcategory->getData('faq_list_position')] = $_subcategory;
                }
            }
        }

        ksort($_subcategories);
        $productData = [];
        foreach ($_subcategories as $category) {
            $products = $category->getProductCollection()->addAttributeToSelect(['name'])
                ->addAttributeToFilter('entity_id', ['in' => $productIds])
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->addAttributeToSort('position', 'ASC');
            foreach ($products as $product) {
                $productData[$category->getName()][] = ['id' => $product->getId(), 'name' => $product->getName()];
            }
        }

        return $this->jsonHelper->jsonEncode($productData);
    }
}
