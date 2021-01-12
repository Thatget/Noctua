<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Swissup\Easytabs\Helper;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Swissup\Easytabs\Model\EntityFactory;
use Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory;

/*
 * @api
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	/**
	 * @var Magento\Framework\App\RequestInterface
	 */
	protected $_request; //only if this is not a controller

	/**
	 * @var Magento\Cms\Model\PageFactory
	 */
	protected $pageFactory;

	/**
	 * @var Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * Get tabs collection
	 * @var \Swissup\Easytabs\Model\ResourceModel\Entity\CollectionFactory
	 */
	protected $tabsCollectionFactory;

	/**
	 * @var \Swissup\Easytabs\Model\EntityFactory
	 */
	protected $entityFactory;

	/**
	 * @var \SIT\GeneralTechnologiesNew\Model\GeneralTechnologyFactory
	 */
	protected $generalTechFactory;

	/**
	 * @param Context           $context               [description]
	 * @param CollectionFactory $tabsCollectionFactory [description]
	 * @param RequestInterface  $request               [description]
	 * @param EntityFactory     $entityFactory         [description]
	 * @param Registry          $coreRegistry          [description]
	 * @param PageFactory       $pageFactory           [description]
	 * @param \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory $generalTechFactory [description]
	 */
	public function __construct(
		Context $context,
		CollectionFactory $tabsCollectionFactory,
		RequestInterface $request,
		EntityFactory $entityFactory,
		Registry $coreRegistry,
		\SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory $generalTechFactory,
		PageFactory $pageFactory

	) {
		$this->_coreRegistry = $coreRegistry;
		$this->tabsCollectionFactory = $tabsCollectionFactory;
		$this->_request = $request;

		$this->pageFactory = $pageFactory;
		$this->entityFactory = $entityFactory;
		$this->generalTechFactory = $generalTechFactory;
		parent::__construct($context);
	}

	/**
	 * To get visible tab

	 */
	public function getVisible($tabId) {
		$collection = $this->entityFactory->create()->load($tabId, 'tab_id');
		$visible = $collection->getVisibleTab();
		if ($visible) {
			if ($visible == 'specific_category') {
				return $collection->getVisibleKey();
			}
			if ($visible == 'specific_product') {
				return $collection->getVisibleKey();
			}
			if ($visible == 'cms_page') {
				return $collection->getVisibleKey();
			}
			if ($visible == 'custom_page') {
				return $collection->getVisibleKey();
			} else {
				return $visible;
			}
		} else {
			return '';
		}
	}

	/**
	 * To get remove from tabs data

	 */
	public function getRemoveFrom($tabId) {
		$collection = $this->entityFactory->create()->load($tabId, 'tab_id');
		$removeFrom = $collection->getRemoveFrom();
		if ($removeFrom) {
			return $removeFrom;
		} else {
			return '';
		}
	}

	/**
	 * To get current product

	 */
	public function getProduct() {
		return $product = $this->_coreRegistry->registry('current_product');
	}

	/**
	 * To get current category

	 */
	public function getCurrentCategory() {
		return $product = $this->_coreRegistry->registry('current_category');
	}

	/**
	 * To get current page url key

	 */
	public function getCurrentPageUrlKey() {
		try {

			$pageId = $this->_request->getParam('page_id');
			if ($pageId) {

				$page = $this->pageFactory->create()->load($pageId);
				return $page->getIdentifier();
			}
		} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
			return null;
		}
	}
/**
 * get Custom router / custom controller page.
 */
	public function getCustomPageInfo($page, $tabId) {
		$decodePage = json_decode($page);
		$pageKey = $this->_request->getModuleName() . '/' . $this->_request->getControllerName() . '/' . $this->_request->getActionName();
		if (is_array($decodePage)) {
			$pageArr = $decodePage;
		}
		if (in_array($pageKey, $pageArr)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * To get cms pages tabs information
	 */
	public function getCmsPageInfo($page, $tabId) {
		$decodeTab = json_decode($page);
		$pageKey = $this->getCurrentPageUrlKey();
		if (is_array($decodeTab)) {
			$pageArr = $decodeTab;
		} else {
			$pageArr = explode(",", $page);
		}
		if (in_array($pageKey, $pageArr)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * To get product tabs information
	 */
	public function getProductInfo($page, $notvisible = "null") {
		$product = $this->getProduct();
		if (!$product) {
			return false;
		}
		$pageArr = explode(",", $page);
		if (in_array("products", $pageArr)) {
			return true;
		} else {

			return false;
		}

	}

	/**
	 * To get all page informaton
	 */
	public function getPageInfo($page, $notvisible = "null") {

		$decodePage = json_decode($page);
		$decodeNtPage = json_decode($notvisible);
		$category = $this->getCurrentCategory();
		$product = $this->getProduct();
		if ($product) {
			return false;
		}
		if (!$category) {
			return false;
		}
		$urlKey = $category->getUrlKey();
		if (is_array($decodePage)) {
			$pageArr = $decodePage;
		} else {
			$pageArr = explode(",", $page);
		}
		if (is_array($decodeNtPage)) {
			$notvisiblepage = $decodeNtPage;
		} else {
			$notvisiblepage = explode(",", $notvisible);
		}
		if (in_array($urlKey, $notvisiblepage)) {
			return false;
		}
		if (in_array($urlKey, $pageArr)) {
			return true;
		} elseif (in_array("category", $pageArr)) {
			return true;
		} elseif (in_array("products", $pageArr) && $product) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * To check child tab is exists or not
	 */
	public function hasChildTab($parent) {
		$childtab = $this->getChildTabs($parent);
		if (count($childtab) > 0) {
			return true;
		}
		return false;

	}

	/**
	 * To get child tabs

	 */
	public function getChildTabs($parent) {
		$childTabs = [];
		$collection = $this->tabsCollectionFactory->create();
		$collection->addFieldToFilter('parent_tab', array('notnull' => true));
		foreach ($collection as $key => $tabs) {
			$tabs->getParentTab() ? $tabs['parent_tab'] = explode(',', $tabs->getParentTab()) : '';
			if (in_array($parent, $tabs['parent_tab'])) {
				$childTabs[$key] = $tabs->getData();
			}
		}
		return $childTabs;
	}
	/** Change By JM [For display Technologies page in custom page] */
	public function getCustomTabs($techId) {
		$techColl = $this->generalTechFactory->create()
			->addAttributeToSelect('*')
			->addFieldToFilter('entity_id', ['eq' => $techId])
			->addAttributeToFilter('status', ['eq' => 1])->getFirstItem();
		return $techColl->getUrlKey();
	}
	/** Change By JM [For display Technologies page in custom page] */

	/**
	 * To find string is json or not

	 */
	public function isJSON($string) {
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
}
