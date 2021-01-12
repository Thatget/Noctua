<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace Mediarocks\ProSlider\Block\Frontend;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mediarocks\ProSlider\Block\Frontend\Slide;
use Mediarocks\ProSlider\Model\SliderFactory;

/**
 *Class Page
 */
class Page extends Template {

	/**
	 * page controller action home
	 */
	const PAGE_ACTION_HOME = 'cms/index/index';

	/**
	 * page controller action cms page
	 */
	const PAGE_ACTION_CMS = 'cms/page/view';

	/**
	 * page controller action category
	 */
	const PAGE_ACTION_CATEGORY = 'catalog/category/view';

	/**
	 * page controller action product
	 */
	const PAGE_ACTION_PRODUCT = 'catalog/product/view';

	/**
	 * page controller 404
	 */
	const PAGE_ACTION_NO_ROUTE = 'cms/noroute/index';

	/**
	 * home page
	 */
	const PAGE_TYPE_CMS = 'CMS';

	/**
	 * category page
	 */
	const PAGE_TYPE_CATEGORY = 'Category';

	/**
	 * product page
	 */
	const PAGE_TYPE_PRODUCT = 'Product';

	/**
	 * product page
	 */
	const PAGE_TYPE_CUSTOM = 'Custom';

	/**
	 * @var SliderFactory
	 */
	protected $sliderFactory;

	/**
	 * @var Registry
	 */
	protected $registry;

	/**
	 * @var StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var Page
	 */
	protected $page;

	/**
	 * @var Http
	 */
	protected $request;

	/**
	 * @var Slide
	 */
	protected $slideCollection;

	/**
	 * [__construct description]
	 * @param Context                 $context         [description]
	 * @param SliderFactory           $sliderFactory   [description]
	 * @param Registry                $registry        [description]
	 * @param Slide                   $slideCollection [description]
	 * @param StoreManagerInterface   $storeManager    [description]
	 * @param \Magento\Cms\Model\Page $page            [description]
	 * @param Http                    $request         [description]
	 */
	public function __construct(
		Context $context,
		SliderFactory $sliderFactory,
		Registry $registry,
		Slide $slideCollection,
		StoreManagerInterface $storeManager,
		\Magento\Cms\Model\Page $page,
		Http $request
	) {
		$this->slideCollection = $slideCollection;
		$this->sliderFactory = $sliderFactory;
		$this->registry = $registry;
		$this->storeManager = $storeManager;
		$this->page = $page;
		$this->request = $request;
		parent::__construct($context);
	}

	/**
	 * To get current page_url for cms pages
	 *
	 * @return String
	 */
	public function getCurrentPageUrlKey() {
		$page_url = $this->page->getIdentifier();
		return $page_url;
	}

	/**
	 * To get current product sku
	 *
	 * @return String
	 */
	public function getProductSku() {
		return $this->registry->registry('current_product')->getSku();
	}

	/**
	 * To get current page category url_key
	 *
	 * @return String
	 */
	public function getCategoryUrlKey() {
		return $this->registry->registry('current_category')->getUrlKey();
	}

	/**
	 * To get current page action
	 *
	 * @return String
	 */
	public function getPageAction() {
		$pageAction = $this->request->getModuleName() . '/' . $this->request->getControllerName() . '/' . $this->request->getActionName();
		return $pageAction;
	}

	/**
	 * To get slider collection
	 *
	 * @return array
	 */
	public function getSliderCollection($pageType) {
		$sliderCollection = $this->sliderFactory->create()->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('page_type', ['eq' => $pageType])->setOrder('updated_at', 'DESC')->load();
		return $sliderCollection;
	}

	/**
	 * To get current page store_id
	 *
	 * @return int
	 */
	public function getStoreId() {
		return $this->storeManager->getStore()->getId();
	}

	/**
	 * @param  string $pageAction [current page type]
	 * @return array              [slide collection]
	 */
	public function getSlider($pageAction, $params) {

		$store = $this->getStoreId();

		/* Added [For Home Page] [20.04.2019] */
		if ($pageAction === self::PAGE_ACTION_HOME) {
			$sliderCollection = $this->getSliderCollection(self::PAGE_TYPE_CMS);
			$dataValue = 'cms_page';
			$url_key = 'home';
			return $this->getSlideData($sliderCollection, self::PAGE_TYPE_CMS, $dataValue, $store, $url_key);
		}
		/* End [For Home Page] [20.04.2019] */

		/* Added [For CMS Page] [20.04.2019] */
		if ($pageAction === self::PAGE_ACTION_CMS) {
			$sliderCollection = $this->getSliderCollection(self::PAGE_TYPE_CMS);
			$dataValue = 'cms_page';
			$url_key = $this->getCurrentPageUrlKey();
			return $this->getSlideData($sliderCollection, self::PAGE_TYPE_CMS, $dataValue, $store, $url_key);
		}
		/* End [For CMS Page] [20.04.2019] */

		/* Added [For CMS 404 Page] [03.05.2019] */
		if ($pageAction === self::PAGE_ACTION_NO_ROUTE) {
			$sliderCollection = $this->getSliderCollection(self::PAGE_TYPE_CMS);
			$dataValue = 'cms_page';
			$url_key = self::PAGE_ACTION_NO_ROUTE;
			return $this->getSlideData($sliderCollection, self::PAGE_TYPE_CMS, $dataValue, $store, $url_key);
		}
		/* End [For CMS 404 Page] [03.05.2019] */

		/* Added [For Categoty Page] [20.04.2019] */
		if ($pageAction === self::PAGE_ACTION_CATEGORY) {
			$sliderCollection = $this->getSliderCollection(self::PAGE_TYPE_CATEGORY);
			$dataValue = 'category';
			$url_key = $this->getCategoryUrlKey();
			return $this->getSlideData($sliderCollection, self::PAGE_TYPE_CATEGORY, $dataValue, $store, $url_key);
		}
		/* End [For Categoty Page] [20.04.2019] */

		/* Added [For Product Page] [20.04.2019] */
		if ($pageAction === self::PAGE_ACTION_PRODUCT) {
			$sliderCollection = $this->getSliderCollection(self::PAGE_TYPE_PRODUCT);
			$dataValue = 'sku';
			$url_key = $this->getProductSku();
			return $this->getSlideData($sliderCollection, self::PAGE_TYPE_PRODUCT, $dataValue, $store, $url_key);
		}
		/* End [For Product Page] [20.04.2019] */

		/* Added [For Custom Page] [26.04.2019] */
		if ($pageAction != self::PAGE_ACTION_HOME && $pageAction != self::PAGE_ACTION_CMS && $pageAction != self::PAGE_ACTION_CATEGORY && $pageAction != self::PAGE_ACTION_PRODUCT && $pageAction != self::PAGE_ACTION_NO_ROUTE) {
			$sliderCollection = $this->getSliderCollection(self::PAGE_TYPE_CUSTOM);
			$dataValue = 'custom_page';
			if ($params == null) {
				$url_key = $pageAction;
			} else {
				$newstring = str_replace("/", " ", $pageAction);
				if (strpos($newstring, 'webforms') !== false) {
					$url_key = $pageAction . '/id/' . $params . '/';
				} else {
					$url_key = $pageAction;
				}
			}
			return $this->getSlideData($sliderCollection, self::PAGE_TYPE_CUSTOM, $dataValue, $store, $url_key);
		}
		/* End [For Custom Page] [26.04.2019] */

	}

	/**
	 * @param  array  $sliderCollection [slider collection]
	 * @param  string $pageType         [current page type]
	 * @param  string $dataValue        [database field name]
	 * @param  int    $store            [store id]
	 * @param  string $url-Key          [current url key]
	 * @return array                    [slide collection]
	 */
	public function getSlideData($sliderCollection, $pageType, $dataValue, $store, $url_key) {
		$img_url = [];
		if (isset($sliderCollection)) {
			$record_counter = 0;
			$i = 0;
			if ($url_key === self::PAGE_ACTION_NO_ROUTE) {
				$url_key = 'no-route';
			}
			foreach ($sliderCollection as $key => $value) {
				$data = $value->getData();
				if ($data['page_type'] === $pageType && $data['is_active'] == 1 && in_array($store, json_decode($data['store_id'])) && $i == 0) {
					$data['slides'] = explode(',', $data['slides']);
					$data[$dataValue] = json_decode(unserialize($data[$dataValue]));
					if (in_array($url_key, $data[$dataValue])) {
						$record_counter++;
						if ($record_counter === 1) {
							foreach ($data['slides'] as $slide_id) {
								$data['slides'][$i] = $this->slideCollection->getSlideCollectionById($slide_id);
								$i++;
							}
							return $data;
						}
					}
				}
			}
		}
	}
}