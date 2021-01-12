<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Model;

class Indexer implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface {
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
	protected $reviewcollFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * @var \SIT\MainAdmin\Helper\Data
	 */
	protected $sitHelper;

	/**
	 * @var \SIT\ProductReviewNew\Model\AwardFactory
	 */
	protected $awardColl;

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resource;

	/**
	 * @param \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                                $storeManager      [description]
	 * @param \Magento\Catalog\Model\ProductFactory                                     $productFactory    [description]
	 * @param \SIT\MainAdmin\Helper\Data                                                $sitHelper         [description]
	 */
	public function __construct(
		\SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewcollFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\SIT\MainAdmin\Helper\Data $sitHelper,
		\SIT\ProductReviewNew\Model\AwardFactory $awardColl,
		\Magento\Framework\App\ResourceConnection $resource
	) {
		$this->reviewcollFactory = $reviewcollFactory;
		$this->storeManager = $storeManager;
		$this->productFactory = $productFactory;
		$this->sitHelper = $sitHelper;
		$this->awardColl = $awardColl;
		$this->resource = $resource;
	}
	/**
	 * Used by mview, allows process indexer in the "Update on schedule" mode
	 */
	public function execute($ids) {
		$this->getListAwardData();
	}

	/**
	 * Will take all of the data and reindex
	 * Will run when reindex via command line
	 */
	public function executeFull() {
		$this->getListAwardData();
	}

	/**
	 * Works with a set of entity changed (may be massaction)
	 */
	public function executeList(array $ids) {
		$this->getListAwardData();
	}

	/**
	 * Works in runtime for a single entity using plugins
	 */
	public function executeRow($id) {
		$this->getListAwardData();
	}

	protected function getListAwardData() {
		$input = readline("Do you want to update all product's awards? (Y/N) : ");
		$digit = strlen($input);
		if (($digit == 1) && ($input == 'y' || $input == 'Y')) {
			$connection = $this->resource->getConnection();
			$connection->truncateTable('sit_productawards_flat');

			$currentStore = $this->storeManager->getStore()->getId();
			$reviewData = $this->reviewcollFactory->create();
			$reviewColl = $reviewData->setStoreId($currentStore)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1])->addAttributeToFilter('review_position', ['gt' => 0]);
			$reviewColl->setOrder('review_position', 'desc');
			$reviewColl->setOrder('created_at', 'desc');

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

			/**
			 * END : Join Query For Add Product Name : RH
			 */

			$awardCollection = [];
			foreach ($reviewColl as $key => $value) {
				if (isset($review_website) && in_array($value->getReviewWebsite(), $review_website)) {
					continue;
				} else {
					$review_website[] = $value->getReviewWebsite();
				}

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
					$awardCollection[$key]['review_image'] = 'productreview/image' . $value->getReviewImage();
				}
			}

			foreach ($awardCollection as $key => $value) {
				$awardFactory = $this->awardColl->create();
				$awardFactory->setProductId($value['product_id']);
				$awardFactory->setProductName($value['product_name']);
				$awardFactory->setReviewWebsite($value['review_website']);
				$awardFactory->setReviewWebsiteLink($value['review_website_link']);
				$awardFactory->setReviewImage($value['review_image']);
				$awardFactory->save();
			}
		}
	}
}