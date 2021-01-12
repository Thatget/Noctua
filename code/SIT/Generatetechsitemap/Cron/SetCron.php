<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Generatetechsitemap\Cron;

use Magento\Framework\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use SIT\GeneralNews\Model\NewsFactory;
use SIT\GeneralTechnologiesNew\Model\GeneralTechnologyFactory;
use SIT\ProductFaqNew\Model\ProductFaqFactory;

class SetCron {

	const SITEMAP_DECLARATION = "<?xml version='1.0' encoding='UTF-8'?><urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:content='http://www.google.com/schemas/sitemap-content/1.0' xmlns:image='http://www.google.com/schemas/sitemap-image/1.1'>";

	/**
	 * @var SIT\GeneralTechnologiesNew\Model\GeneralTechnologyFactory
	 */
	protected $generalTechFactory;

	/**
	 * @var SIT\ProductFaqNew\Model\ProductFaqFactory
	 */
	protected $productFaqFactory;

	/**
	 * @var SIT\GeneralNews\Model\NewsFactory
	 */
	protected $newsFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var Magento\Framework\Filesystem\DirectoryList
	 */
	protected $_dir;

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $productColl;

	/**
	 * @param GeneralTechnologyFactory                                       $generalTechFactory [description]
	 * @param ProductFaqFactory                                              $productFaqFactory  [description]
	 * @param NewsFactory                                                    $newsFactory        [description]
	 * @param DirectoryList                                                  $dir                [description]
	 * @param StoreManagerInterface                                          $storeManager       [description]
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productColl        [description]
	 */
	public function __construct(
		GeneralTechnologyFactory $generalTechFactory,
		ProductFaqFactory $productFaqFactory,
		NewsFactory $newsFactory,
		DirectoryList $dir,
		StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productColl
	) {
		$this->generalTechFactory = $generalTechFactory;
		$this->productFaqFactory = $productFaqFactory;
		$this->newsFactory = $newsFactory;
		$this->_storeManager = $storeManager;
		$this->_dir = $dir;
		$this->prodCompHelper = $prodCompHelper;
		$this->productColl = $productColl;
	}

	public function setCron() {
		$generalTechCollection = $this->generalTechFactory->create()->getCollection()->addAttributeToSelect('url_key');
		$productFaqCollection = $this->productFaqFactory->create()->getCollection()->addAttributeToSelect('url_key');
		$newsCollection = $this->newsFactory->create()->getCollection()->addAttributeToSelect('url_key');

		$modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
		$modelId = $modelInfo->getAttributeId();
		$modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
		$optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($gridType));
		$compCpuCollection = $this->collFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('comp_type', ['eq' => $optionId]);
		$compCpuCollection->getSelect()->joinLeft(
			['productTable' => 'sit_productcompatibility_productcompatibility_product'],
			'e.entity_id = productTable.productcompatibility_id',
			['productTable.product_id']
		);
		$product_ids = [];
		foreach ($compCpuCollection->getData() as $key => $value) {
			if (!in_array($value['product_id'], $product_ids)) {
				$product_ids[] = $value['product_id'];
			}
		}
		$productObj = $this->productColl->create()->addAttributeToSelect('*')->addAttributeToFilter('entity_id', ['in' => $product_ids]);

		$storeCollection = $this->_storeManager->getStores();
		$base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		$path = $this->_dir->getRoot();
		$fileName = $path . '/' . 'sites_';
		$this->generateSitemap($storeCollection, $generalTechCollection, $base_url, $fileName . 'general_tech.xml');
		$this->generateSitemap($storeCollection, $newsCollection, $base_url, $fileName . 'news.xml');
		$this->generateSitemap($storeCollection, $productFaqCollection, $base_url, $fileName . 'faq.xml');
		$this->generateSitemapCpu($storeCollection, $productObj, $base_url, $fileName . 'comp_cpu.xml');
	}

	/**
	 * To generate sitemap for different category
	 * @param  String $storeCollection [description]
	 * @param  String $collection      [description]
	 * @param  String $base_url        [description]
	 * @param  String $fileName        [description]
	 */
	public function generateSitemap($storeCollection, $collection, $base_url, $fileName) {
		$sitemap = self::SITEMAP_DECLARATION;
		$sitempa_tag = "";
		try {
			if (isset($collection)) {
				foreach ($storeCollection as $key => $store) {
					$storeCode = $store->getCode() . '/';
					foreach ($collection as $key => $value) {
						$sitempa_tag .= "<url>";
						$sitempa_tag .= "<loc>" . $base_url . $storeCode . $value->getUrlKey() . "</loc>";
						$sitempa_tag .= "<lastmod>" . date("Y-m-d") . "</lastmod>";
						$sitempa_tag .= "<changefreq>daily</changefreq>";
						$sitempa_tag .= "<priority>0</priority>";
						$sitempa_tag .= "</url>";
					}
				}
				$sitemap .= $sitempa_tag . "</urlset>";
			}
			$handle = fopen($fileName, 'w') or die('Cannot open file:  ' . $fileName);
			fwrite($handle, $sitemap);
		} catch (Exception $e) {
			echo 'Message: Sitemap Not Generated' . $e->getMessage();
		}
	}

	/**
	 * To generate sitemap for cpu
	 * @param  String $storeCollection [description]
	 * @param  String $collection      [description]
	 * @param  String $base_url        [description]
	 * @param  String $fileName        [description]
	 */
	public function generateSitemapCpu($storeCollection, $collection, $base_url, $fileName) {
		$sitemap = self::SITEMAP_DECLARATION;
		$sitempa_tag = "";
		try {
			if (isset($collection)) {
				foreach ($storeCollection as $key => $store) {
					$storeCode = $store->getCode() . '/';
					foreach ($collection as $key => $value) {
						$sitempa_tag .= "<url>";
						$sitempa_tag .= "<loc>" . $base_url . $storeCode . $value->getUrlKey() . "</loc>";
						$sitempa_tag .= "<lastmod>" . date("Y-m-d") . "</lastmod>";
						$sitempa_tag .= "<changefreq>daily</changefreq>";
						$sitempa_tag .= "<priority>0</priority>";
						$sitempa_tag .= "</url>";
					}
				}
				$sitemap .= $sitempa_tag . "</urlset>";
			}
			$handle = fopen($fileName, 'w') or die('Cannot open file:  ' . $fileName);
			fwrite($handle, $sitemap);
		} catch (Exception $e) {
			echo 'Message: Sitemap Not Generated' . $e->getMessage();
		}
	}
}