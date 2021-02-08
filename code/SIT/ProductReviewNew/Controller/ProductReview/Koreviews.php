<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\ProductReview;

use Magento\Framework\Controller\ResultFactory;

class Koreviews extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    private $reviewcollFactory;

    /**
     * @var \SIT\MainAdmin\Helper\Data
     */
    private $sitHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context                                     $context               [description]
     * @param \Magento\Framework\Json\Helper\Data                                       $jsonHelper            [description]
     * @param \Magento\Store\Model\StoreManagerInterface                                $storeManagerInterface [description]
     * @param \Magento\Eav\Model\Config                                                 $eavConfig             [description]
     * @param \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory     [description]
     * @param \SIT\MainAdmin\Helper\Data                                                $sitHelper             [description]
     * @param \Magento\Framework\View\Result\PageFactory                                $resultPageFactory     [description]
     * @param \Magento\Catalog\Model\CategoryFactory                                    $categoryFactory       [description]
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                      $timezone              [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Eav\Model\Config $eavConfig,
        \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory,
        \SIT\MainAdmin\Helper\Data $sitHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->eavConfig = $eavConfig;
        $this->reviewcollFactory = $reviewcollFactory;
        $this->sitHelper = $sitHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFactory = $categoryFactory;
        $this->timezone = $timezone;
        parent::__construct($context);
    }

    /**
     * Changes by PP [START] : 5-4-2019
     */

    /**
     * Return array of reviews and language list
     *
     * @return array
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $paramArray = [];
        if ($this->getRequest()->getContent()) {
            $paramArray = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
            if (array_key_exists('reviewlist', $paramArray)) {
                return $this->getKoReviews($paramArray);
            }
            if (array_key_exists('languageList', $paramArray)) {
                return $this->getAllLanguage();
            }

        }
    }

    /**
     * Return all languages for review page
     *
     * @return \Magento\Framework\Controller\ResultFactory
     */
    private function getAllLanguage()
    {
        $attribute = $this->eavConfig->getAttribute('sit_productreviewnew_productreview', 'r_lng');
        $language = $attribute->getSource()->getAllOptions();

        $options = [];
        $language[0] = ['value' => "All", 'label' => "All"];
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode($language));
        return $response;
    }

    /**
     * Return reviews collection
     *
     * @return \Magento\Framework\Controller\ResultFactory
     */
    private function getKoReviews($paramArray)
    {
        $attributeId = $this->eavConfig->getAttribute('sit_productreviewnew_productreview', 'review_position');
        $currentStore = $this->storeManagerInterface->getStore()->getStoreId();
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
        $reviewColl->getSelect()->joinLeft(
            ['pv2' => 'sit_productreviewnew_productreview_varchar'],
            'e.entity_id = pv2.entity_id and if( pv2.store_id = '.$currentStore.' ,pv2.store_id = '.$currentStore.',pv2.store_id = 0 ) and pv2.attribute_id = ' . $attributeId->getId(),
            'CAST(pv2.value AS DECIMAL(10,2)) as product_review_new'
        );
        /**
         * END : Join Query For Add Product Name : RH
         */
        $reviewColl->setOrder('created_at', 'desc');
        $reviewColl->setOrder('entity_id', 'desc');

        /**
         * Product filter for review
         */
        if ($paramArray["pid"] != "All") {
            $reviewColl->getSelect()->where('t2.product_id = ? ', $paramArray["pid"]);
        }
        /**
         * Language filter for review
         */
        if ($paramArray["lid"] != "All") {
            $reviewColl->addAttributeToFilter('r_lng', ['eq' => $paramArray["lid"]]);
        }
        /**
         * Set current page and page size
         */
        $reviewColl->setPageSize($paramArray["limit"])->setCurPage($paramArray["p"]);

        $reviewColl->getSelect()->order('product_review_new DESC');
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
            $reviewArray["review_image"] = $this->getReviewImage($review->getReviewImage());
            $reviewArray["review_pname"] = $review->getPname();
            $reviewArray["review_desc"] = $review->getReviewDesc();
            $reviewArray["review_short_desc"] = $review->getReviewShortDesc();
            $reviewArray["review_r_lng"] = $options[$review->getRLng()];
            $reviewArray["review_r_lng_id"] = $review->getRLng();
            array_push($reviewsKoArray, $reviewArray);
        }
        /**
         * Get size of collection
         */
        $totalProductReviews = $reviewData->getSize();
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode(["collection" => $reviewsKoArray, "size" => $totalProductReviews]));
        return $response;
    }

    /**
     * Return product review images
     *
     * @param  string $image
     * @return \SIT\MainAdmin\Helper\Data
     */
    private function getReviewImage($image)
    {
        return $this->sitHelper->getImage('productreview/image', $image);
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
     * Changes by PP [END] : 5-4-2019
     */
}
