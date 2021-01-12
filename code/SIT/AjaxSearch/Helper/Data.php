<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\AjaxSearch\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $jsonFactory;

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
	 * @var \Magento\Catalog\Helper\Image
	 */
	protected $imageHelper;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\Json\Helper\Data                            $jsonHelper               [description]
	 * @param \Magento\Framework\Controller\Result\JsonFactory               $jsonFactory              [description]
	 * @param \Magento\Catalog\Model\ProductFactory                          $productFactory           [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager             [description]
	 * @param \SIT\MainAdmin\Helper\Data                                     $sitHelper                [description]
	 * @param \Magento\Catalog\Helper\Image                                  $imageHelper              [description]
	 */
	public function __construct(
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\SIT\MainAdmin\Helper\Data $sitHelper,
		\Magento\Catalog\Helper\Image $imageHelper
	) {
		$this->jsonHelper = $jsonHelper;
		$this->jsonFactory = $jsonFactory;
		$this->productFactory = $productFactory;
		$this->storeManager = $storeManager;
		$this->sitHelper = $sitHelper;
		$this->imageHelper = $imageHelper;
	}

	public function getProductByCategory($catUrlKey) {
		$productArray = [];
		$productCollection = $this->sitHelper->getProductByCategory($catUrlKey);
		$productInstance = $this->productFactory->create();
		$count = 0;
		foreach ($productCollection as $key => $value) {
			$product = $this->productFactory->create()->load($value['entity_id']);
			$productArray[$count]['id'] = $value['entity_id'];
			$productArray[$count]['name'] = $product->getName();
			$productArray[$count]['product_url'] = $product->getProductUrl();
			$productArray[$count]['cmscontent'] = $this->sitHelper->getCmsFilterContent($product->getData('feature_1_en'));
			$productArray[$count]['left_image'] = $this->imageHelper->init($product, 'small_image')->setImageFile($product->getSmallImage())->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepTransparency(TRUE)->keepFrame(FALSE)->resize(100)->getUrl();
			$productArray[$count]['right_image'] = $this->imageHelper->init($product, 'thumbnail')->setImageFile($product->getSmallImage())->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepTransparency(TRUE)->keepFrame(FALSE)->resize(100)->getUrl();
			$count++;
		}
		return $productArray;
	}
}
