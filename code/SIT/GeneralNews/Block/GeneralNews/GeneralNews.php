<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Block\GeneralNews;

class GeneralNews extends \Magento\Framework\View\Element\Template {
	/**
	 * Home Product category url key
	 */
	const CATEGORY_URL_KEY = 'home-product';

	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;

	/**
	 * @var \SIT\GeneralNews\Model\ResourceModel\GeneralNews\CollectionFactory
	 */
	protected $_newscollFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $_timezone;

	/**
	 * @var \Magento\Cms\Model\Template\FilterProvider
	 */
	protected $_filterProvider;

	/**
	 * @var \Magento\Catalog\Model\CategoryFactory
	 */
	protected $_categoryFactory;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $_productFactory;

	/**
	 * @var \Magento\Catalog\Helper\Image
	 */
	protected $_imageHelper;

	/**
	 * @var \SIT\MainAdmin\Helper\Data
	 */
	protected $sitHelper;

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_storeConfig;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\Template\Context            $context         [description]
	 * @param \Magento\Framework\App\Request\Http                         $request         [description]
	 * @param \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory $newscollFactory [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager    [description]
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface        $timezone        [description]
	 * @param \Magento\Cms\Model\Template\FilterProvider                  $filterProvider  [description]
	 * @param \Magento\Catalog\Model\CategoryFactory                      $categoryFactory [description]
	 * @param \Magento\Catalog\Model\ProductFactory                       $productFactory  [description]
	 * @param \Magento\Catalog\Helper\Image                               $imageHelper     [description]
	 * @param \SIT\MainAdmin\Helper\Data                                  $sitHelper       [description]
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig     [description]
	 * @param array                                                       $data            [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\Request\Http $request,
		\SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory $newscollFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Helper\Image $imageHelper,
		\SIT\MainAdmin\Helper\Data $sitHelper,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		array $data = []
	) {
		$this->request = $request;
		$this->_newscollFactory = $newscollFactory;
		$this->_storeManager = $storeManager;
		$this->_timezone = $timezone;
		$this->_filterProvider = $filterProvider;
		$this->_categoryFactory = $categoryFactory;
		$this->_productFactory = $productFactory;
		$this->_imageHelper = $imageHelper;
		$this->sitHelper = $sitHelper;
		$this->_storeConfig = $scopeConfig;
		parent::__construct($context, $data);
	}

	/**
	 * Collection of main page news
	 *
	 * @return \SIT\GeneralNews\Model\ResourceModel\GeneralNews\CollectionFactory
	 */
	public function getMainNewsData() {
		$currentStore = $this->_storeManager->getStore()->getId();
		$mainNewsData = $this->_newscollFactory->create();
		$newsColl = $mainNewsData->setStoreId($currentStore)
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', ['eq' => 1])
			->setOrder('created_at', 'desc')
			->setPageSize(2);
		return $newsColl;
	}

	/**
	 * Get collection on Home Product Category
	 *
	 * @return \Magento\Catalog\Model\CategoryFactory
	 */

	public function getNewHomeProduct() {
		$category = $this->_categoryFactory->create()->loadByAttribute('url_key', self::CATEGORY_URL_KEY)
			->getProductCollection()
			->addAttributeToSelect('*')
			->addAttributeToSort('position', 'ASC')
			->addAttributeToSort('created_at', 'DESC')
			->addStoreFilter()
			->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
			->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
			->setPageSize(2);
		return $category;
	}

	/**
	 * Change date format for news
	 *
	 * @return date
	 */
	public function changeDateFormat($date) {
		return $this->_timezone->date(new \DateTime($date))->format('d.m.Y');
	}

	/**
	 * Make array format of news data collection
	 *
	 * @return array
	 */
	public function MainNewsArray() {
		$news_array = [];
		$index = 0;
		$newsColl = $this->getMainNewsData();
		foreach ($newsColl as $news => $value) {
			$news_array[$index]['news_title'] = $value->getNewsTitle();
			$news_array[$index]['news_shortdesc'] = $value->getNewsShortdesc();
			$news_array[$index]['created_at'] = $value->getCreatedAt();
			$news_array[$index]['url_key'] = $this->sitHelper->getStoreBaseUrl() . "" . $value->getUrlKey();
			$index++;
		}
		return $news_array;
	}

	/**
	 * Make array format of home product collection
	 *
	 * @return array
	 */
	public function HomeProductArray() {
		$home_pro_array = [];
		$index = 0;
		$homeProColl = $this->getNewHomeProduct();
		$product = $this->_productFactory->create();
		foreach ($homeProColl as $key => $value) {
			$home_pro_array[$index]['name'] = $value->getName();
			$home_pro_array[$index]['url'] = $value->getProductUrl();
			if ($this->_imageHelper->init($product->load($value->getId()), 'image')->setImageFile($value->getImage())->getUrl() == "") {
				$home_pro_array[$index]['image'] = $this->_storeConfig->getValue('catalog/placeholder/small_image_placeholder');
			} else {
				$home_pro_array[$index]['image'] = $this->_imageHelper->init($product->load($value->getId()), 'image')->setImageFile($value->getImage())->getUrl();
			}
			$home_pro_array[$index]['text_new_en'] = $value->getData('text_new_en');
			$home_pro_array[$index]['feature_1_en'] = $value->getData('feature_1_en');
			$index++;
		}
		return $home_pro_array;
	}

	/**
	 * Return news details when custom routing action call
	 *
	 * @return \SIT\GeneralNews\Model\ResourceModel\GeneralNews\CollectionFactory
	 */
	public function getNewsDetails() {
		$currentStore = $this->_storeManager->getStore()->getId();
		$news_id = $this->request->getParam('news_id');
		$newsData = $this->_newscollFactory->create();
		$newsColl = $newsData->setStoreId($currentStore)
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', ['eq' => 1])
			->addAttributeToFilter('entity_id', ['eq' => $news_id])
			->getFirstItem();
		return $newsColl;
	}

    /**
     * Return news details when custom routing action call
     *
     * @return \SIT\GeneralNews\Model\ResourceModel\GeneralNews\CollectionFactory
     */
    public function getCheckNewsDetails() {
        $currentStore = $this->_storeManager->getStore()->getId();
        $news_id = $this->request->getParam('news_id');
        $newsData = $this->_newscollFactory->create();
        $newsColl = $newsData->setStoreId($currentStore)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['eq' => $news_id])
            ->getFirstItem();
        return $newsColl;
    }

	/**
	 * Return news index controller data collection
	 *
	 * @return \SIT\GeneralNews\Model\ResourceModel\GeneralNews\CollectionFactory
	 */
	public function getNewsPageCollection() {
		$index = 0;
		$currentStore = $this->_storeManager->getStore()->getId();
		$mainNewsData = $this->_newscollFactory->create();
		$newsColl = $mainNewsData->setStoreId($currentStore)
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', ['eq' => 1])
			->setOrder('created_at', 'desc');
		$news_arr_coll = [];
		foreach ($newsColl as $key => $value) {
			$news_arr_coll[$index]['news_title'] = $value->getNewsTitle();
			$news_arr_coll[$index]['news_shortdesc'] = $this->sitHelper->getCmsFilterContent($value->getNewsShortdesc());
			$news_arr_coll[$index]['created_at'] = strftime("%A, %B %d, %Y", strtotime($value->getCreatedAt()));
			$news_arr_coll[$index]['news_url'] = $this->sitHelper->getStoreBaseUrl() . "" . $value->getUrlKey();
			$news_arr_coll[$index]['arrow_img'] = $this->sitHelper->getImage('default/images/', 'duble-arrow.png');
			$index++;
		}
		return $news_arr_coll;
	}
	/**
	 * Prepare Content HTML
	 *
	 * @return string
	 */
	public function getFilterableCMSData($newsDesc) {
		return $this->_filterProvider->getBlockFilter()->setStoreId($this->_storeManager->getStore()->getId())->filter($newsDesc);
	}
}
