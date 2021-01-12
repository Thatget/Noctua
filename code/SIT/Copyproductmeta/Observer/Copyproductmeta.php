<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Copyproductmeta\Observer;

use Magento\Framework\Event\ObserverInterface;

class Copyproductmeta implements ObserverInterface {

	/**
	 * for get default description
	 */
	const XML_PATH_DEFAULT_DESCRIPTION = 'design/head/default_description';

	/**
	 * for get default title
	 */
	const XML_PATH_DEFAULT_TITLE = 'design/head/default_title';

	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\Template\Context   $context       [description]
	 * @param \Magento\Framework\Registry                        $registry      [description]
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig   [description]
	 * @param \Magento\Catalog\Helper\Data                       $catalogHelper [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Helper\Data $catalogHelper
	) {
		$this->request = $context->getRequest();
		$this->registry = $registry;
		$this->context = $context;
		$this->scopeConfig = $scopeConfig;
		$this->catalogHelper = $catalogHelper;

	}

	/**
	 * @param  \Magento\Framework\Event\Observer $observer [description]
	 * @return [type]                                      [description]
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$this->pageConfig = $this->context->getPageConfig();
		$this->getDescription();
		$this->getTitle();
	}

	/**
	 * [getDescription get and set desription for meta tag]
	 * @return [type] [description]
	 */
	public function getDescription() {
		$product = $this->registry->registry('current_product');
		if ($product) {
			$description = $product->getMetaDescription();
			$shdescription = $product->getShortDescription();
			if ($description) {
				return $this->pageConfig->setDescription($description);
			} else {
				return $this->pageConfig->setDescription($this->cleanText($shdescription));
			}
		} else {
			if (empty($this->pageConfig->getDescription())) {
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$defaultDescription = $this->scopeConfig->getValue(self::XML_PATH_DEFAULT_DESCRIPTION, $storeScope);
				$this->pageConfig->setDescription($defaultDescription);
			}
			return $this->pageConfig->getDescription();
		}
	}

	/**
	 * [getTitle get and set title for meta tag]
	 * @return [type] [description]
	 */
	public function getTitle() {
		$product = $this->registry->registry('current_product');
		if ($product) {
			$title = $product->getMetaTitle();
			$product_name = $product->getName();
			if ($title) {
				return $this->pageConfig->getTitle()->set($title);
			} else {
				return $this->pageConfig->getTitle()->set($this->cleanText($product_name));
			}
		} else {
			if (empty($this->pageConfig->getTitle()->get())) {
				$this->pageConfig->getTitle()->set($this->getDefaultTitle());
			}
			$title = htmlspecialchars(html_entity_decode(trim($this->pageConfig->getTitle()->get()), ENT_QUOTES, 'UTF-8'));
			return $this->pageConfig->getTitle()->set($title);
		}
	}

	/**
	 * [cleanText loagic for replace text]
	 * @param  [type] $text [description]
	 * @return [type]       [description]
	 */
	public function cleanText($text) {
		$helper = $this->catalogHelper;
		$processor = $helper->getPageTemplateProcessor();
		$html = $processor->filter($text);
		$html = str_ireplace('&nbsp;', ' ', $html);
		$html = str_ireplace('&rsquo;', '\'', $html);
		return strip_tags($html);
	}

	/**
	 * [getDefaultTitle get value from configuration]
	 * @return [type] [description]
	 */
	public function getDefaultTitle() {
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		return $defaultTitle = $this->scopeConfig->getValue(self::XML_PATH_DEFAULT_TITLE, $storeScope);
	}
}
