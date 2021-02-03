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

class View extends \Magento\Framework\View\Element\Template
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
     * @var \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    protected $_reviewcollFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \SIT\MainAdmin\Helper\Data
     */
    protected $sitHelper;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context                          $context           [description]
     * @param \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                                $storeManager      [description]
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                      $timezone          [description]
     * @param \Magento\Catalog\Model\ProductFactory                                     $productFactory    [description]
     * @param \SIT\MainAdmin\Helper\Data                                                $sitHelper         [description]
     * @param \Magento\Eav\Model\Config                                                 $eavConfig         [description]
     * @param array                                                                     $data              [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \SIT\MainAdmin\Helper\Data $sitHelper,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->_reviewcollFactory = $reviewcollFactory;
        $this->_storeManager = $storeManager;
        $this->_timezone = $timezone;
        $this->productFactory = $productFactory;
        $this->sitHelper = $sitHelper;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $data);
    }

    /**
     * Latest five review for home page
     *
     * @return \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    public function getHomePageReviewData()
    {
        $currentStore = $this->_storeManager->getStore()->getId();
        $reviewData = $this->_reviewcollFactory->create();
        try {
            $reviewColl = $reviewData->setStoreId($currentStore)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1])->addAttributeToFilter('review_startpage', ['eq' => 1]);
            $reviewColl->setPageSize(5)->setCurPage(1);
        } catch (\Exception $e) {
            // echo $e->getMessage();
            return false;
        }
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

    public function getProductPageReviewData($productId)
    {
        $currentStore = $this->_storeManager->getStore()->getId();
        $reviewData = $this->_reviewcollFactory->create();
        $_product = $this->productFactory->create()->load($productId);

        $reviewColl = $reviewData->setStoreId($currentStore)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1]);
        $reviewColl->setPageSize(10)->setCurPage(1);
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
        $reviewColl->setOrder('product_review_priority', 'desc');
        $reviewColl->setOrder('created_at', 'desc');
        // $reviewColl->setOrder('entity_id', 'desc');
        $reviewColl->getSelect()->where('t2.product_id = ? ', $productId);

        $totalProductReviews = $reviewData->getSize();

        $reviewArray = [];
        $reviewsKoArray = [];
        $attribute = $this->eavConfig->getAttribute('sit_productreviewnew_productreview', 'r_lng');
        $language = $attribute->getSource()->getAllOptions();

        $options = [];
        foreach ($language as $option) {
            if ($option['label'] != ' ') {
                $options[$option['value']] = $option['label'];
            }
        }
        foreach ($reviewColl as $review) {
			$reviewArray['review_position'] = $review->getReviewPosition();
            $reviewArray["created_at"] = $this->changeDateFormat($review->getCreatedAt());
            $reviewArray["review_website"] = $review->getReviewWebsite();
            $reviewArray["review_country"] = $this->sitHelper->getImage('flags', '/' . strtolower($review->getReviewCountry() . '.gif'));
            $reviewArray["review_link"] = $review->getReviewLink();
            $reviewArray["review_image"] = $this->sitHelper->getImage('productreview/image', $review->getReviewImage());
            $reviewArray["review_pname"] = $review->getPname();
            $reviewArray["review_desc"] = $review->getReviewDesc();
            $reviewArray["review_short_desc"] = $review->getReviewShortDesc();
            $reviewArray["review_r_lng"] = $options[$review->getRLng()];
            $reviewArray["review_r_lng_id"] = $review->getRLng();
            array_push($reviewsKoArray, $reviewArray);
        }
		        $i = 0;
        foreach($reviewsKoArray as $key => $value){
            $j = 0;
            $i++;
            foreach ($reviewsKoArray as $newKey => $newValue){
                $j++;
                if($j <= $i) continue;
                if ((int)$value['review_position'] < (int)$newValue['review_position']){
                    $x = $reviewsKoArray[$key];
                    $reviewsKoArray[$key] = $reviewsKoArray[$newKey];
                    $reviewsKoArray[$newKey] = $x;
                }
            }
        }
        return [
            "collection" => $reviewsKoArray,
            "totalreviews" => $totalProductReviews,
            "text" => __('See all %d reviews for this product', ['d' => $totalProductReviews])
        ];
    }
    /**
     * Change date format for review
     *
     * @return date
     */
    public function changeDateFormat($date)
    {
        return $this->_timezone->date(new \DateTime($date))->format('d.m.Y');
    }
}
