<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace Henhed\Piwik\Block\Adminhtml;

class Piwik extends \Magento\Backend\Block\Template {

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;

	/**
	 * @param \Magento\Backend\Block\Template\Context            $context     [description]
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig [description]
	 * @param array                                              $data        [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		array $data = []
	) {
		$this->scopeConfig = $scopeConfig;
		parent::__construct($context, $data);
	}

	public function getPiwikIframe() {
		$installPath = $this->scopeConfig->getValue('piwik/tracking/hostname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$siteId = $this->scopeConfig->getValue('piwik/tracking/site_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return "<div id='widgetIframe'><iframe src='" . $installPath . "'/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Dashboard&actionToWidgetize=index&idSite='" . $siteId . "'&period=range&date=last30' frameborder='0' marginheight='0' marginwidth='0' width='100%' height='1000px'></iframe></div>";
	}
}
