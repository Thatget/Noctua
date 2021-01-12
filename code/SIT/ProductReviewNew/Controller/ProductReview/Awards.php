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

class Awards extends \Magento\Framework\App\Action\Action {

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
	 * @var SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
	 */
	protected $reviewcollFactory;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \SIT\MainAdmin\Helper\Data
	 */
	protected $sitHelper;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\View\Asset\Repository
	 */
	protected $_assetRepo;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var \SIT\ProductReviewNew\Model\ResourceModel\Award\CollectionFactory
	 */
	protected $awardCollFactory;

	/**
	 * @param \Magento\Framework\App\Action\Context                                     $context           [description]
	 * @param \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory [description]
	 * @param \Magento\Catalog\Model\ProductFactory                                     $productFactory    [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                                $storeManager      [description]
	 * @param \SIT\MainAdmin\Helper\Data                                                $sitHelper         [description]
	 * @param \Magento\Framework\View\Result\PageFactory                                $resultPageFactory [description]
	 * @param \Magento\Framework\View\Asset\Repository                                  $assetRepo         [description]
	 * @param \Magento\Framework\Controller\Result\JsonFactory                          $resultJsonFactory [description]
	 * @param \SIT\ProductReviewNew\Model\ResourceModel\Award\CollectionFactory         $awardCollFactory  [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\SIT\MainAdmin\Helper\Data $sitHelper,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\Asset\Repository $assetRepo,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\SIT\ProductReviewNew\Model\ResourceModel\Award\CollectionFactory $awardCollFactory
	) {
		$this->reviewcollFactory = $reviewcollFactory;
		$this->storeManager = $storeManager;
		$this->productFactory = $productFactory;
		$this->sitHelper = $sitHelper;
		$this->resultPageFactory = $resultPageFactory;
		$this->_assetRepo = $assetRepo;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->awardCollFactory = $awardCollFactory;
		parent::__construct($context);
	}

	/**
	 * Return array of reviews and language list
	 *
	 * @return array
	 */
	public function execute() {
		$resultPage = $this->resultPageFactory->create();
		$currentStore = $this->storeManager->getStore()->getId();
		$awardCollection = $this->awardCollFactory->create();
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		$collection = [];
		$award_arr = [];
		if ($awardCollection->getSize() > 0) {
			$award_arr = $awardCollection->getData();
		}
		$collection['award_coll'] = $award_arr;
		$collection['media'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$resultJson->setData($collection);
		return $resultJson;
	}

	/**
	 * To get loading image path
	 * @return String
	 */
	public function getViewFileUrl($filePath) {
		return $this->_assetRepo->getUrl($filePath);
	}
}
