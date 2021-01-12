<?php

namespace WebRotate360\ProductViewerStandard\Helper;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	const XML_PATH_ENABLED = 'webrotate360standard/general/enabled';
	const XML_PATH_VIEWER_SKIN = 'webrotate360standard/general/viewer_skin';
	const XML_PATH_USE_ANALYTICS = 'webrotate360standard/advanced/use_analytics';
	const XML_PATH_API_CALLBACK = 'webrotate360standard/advanced/api_callback';
	const XML_PATH_MASTER_CONFIG = 'webrotate360standard/advanced/master_config';
	const XML_PATH_LICENSE = 'webrotate360standard/advanced/license';
	const XML_PATH_POPUP_ICON = 'webrotate360standard/general/popup_icon';
	const XML_PATH_END_PLACEMENT = 'webrotate360standard/general/gallery_end_placement';

	protected $_objectManager = null;
	private $_currProduct = null;
	private $_storeManager = null;
	private $_assetRepo = null;
	private $_prodRepo = null;

	/**
	 * @var CollectionFactory
	 */
	protected $productCollection;

	/**
	 * @param \Magento\Framework\App\Helper\Context      $context           [description]
	 * @param \Magento\Framework\View\Asset\Repository   $assetRepo         [description]
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager      [description]
	 * @param \Magento\Catalog\Model\ProductRepository   $prodRepo          [description]
	 * @param CollectionFactory                          $productCollection [description]
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\View\Asset\Repository $assetRepo,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ProductRepository $prodRepo,
		CollectionFactory $productCollection
	) {
		$this->_storeManager = $storeManager;
		$this->_assetRepo = $assetRepo;
		$this->_prodRepo = $prodRepo;
		$this->productCollection = $productCollection;

		parent::__construct($context);
	}

	private function initCurrentProduct() {
		if (!$this->_objectManager) {
			$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		}

		if (!$this->_currProduct) {
			$this->_currProduct = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');
		}

	}

	public function getBaseUrl() {
		return $this->_storeManager->getStore()->getBaseUrl();
	}

	/** [Added by JM for get base url without store code ] */
	public function getBaseUrlWithoutCode() {
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
	}
	/** [Added by JM for get base url without store code ] */

	public function getWebrotatePath() {
		$this->initCurrentProduct();

		if ($this->_currProduct) {
			return ltrim($this->_currProduct->getData('webrotate_path'), '/');
		}

		return null;
	}
	public function getWebrotateRootUrl() {
		$this->initCurrentProduct();

		if ($this->_currProduct) {
			return $this->_currProduct->getData('webrotate_root');
		}

		return null;
	}

	public function getGraphicsPathUrl() {
		return $this->_assetRepo->getUrl('WebRotate360_ProductViewerStandard::imagerotator/html/img/' . $this->getViewerSkin());
	}

	public function isEnabled() {
		return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getViewerSkin() {
		return $this->scopeConfig->getValue(self::XML_PATH_VIEWER_SKIN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getViewerSkinUrl() {
		return $this->_assetRepo->getUrl('WebRotate360_ProductViewerStandard::imagerotator/html/css/' . $this->getViewerSkin() . '.css');
	}

	public function isEndPlacement() {
		return $this->scopeConfig->isSetFlag(self::XML_PATH_END_PLACEMENT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function isUseAnalytics() {
		return $this->scopeConfig->isSetFlag(self::XML_PATH_USE_ANALYTICS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getApiCallback() {
		return $this->scopeConfig->getValue(self::XML_PATH_API_CALLBACK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getMasterConfigUrl() {
		return $this->scopeConfig->getValue(self::XML_PATH_MASTER_CONFIG, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getLicense() {
		$licPath = $this->scopeConfig->getValue(self::XML_PATH_LICENSE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if (!empty($licPath)) {
			return $licPath;
		}

		return $this->_assetRepo->getUrl('WebRotate360_ProductViewerStandard::imagerotator/license.lic');
	}

	public function getPopupIconUrl() {
		$value = $this->scopeConfig->getValue(self::XML_PATH_POPUP_ICON, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if (empty($value)) {
			return $this->_assetRepo->getUrl('WebRotate360_ProductViewerStandard::360view.png');
		}

		return $value;
	}

	public function getSwatches() {
		$this->initCurrentProduct();

		if (!$this->_currProduct || $this->_currProduct->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
			return null;
		}

		$swatches = (object) [];
		$children = $this->_currProduct->getTypeInstance()->getUsedProducts($this->_currProduct);
		$masterConfig = $this->getMasterConfigUrl();

		foreach ($children as $child) {
			$fetched = $this->_prodRepo->getById($child->getId());
			$configRoot = $fetched->getData('webrotate_root');
			$configURL = ltrim($fetched->getData('webrotate_path'), '/');

			if (!$configURL && $masterConfig && $configRoot) {
				$configURL = $masterConfig;
			}

			if ($configURL) {
				$swatches->{$child->getId()} = array(
					'confFileURL' => $this->getBaseUrl() . $configURL,
					'rootPath' => $configRoot,
				);
			}
		}

		return (count($swatches) > 0) ? json_encode($swatches, JSON_UNESCAPED_SLASHES) : null;
	}

	/**
	 * To get web rotate data from product collection
	 * @return productCollection
	 */
	public function getWebRotateData() {
		$collection = $this->productCollection->create();
		$collection->addAttributeToSelect('*');
		return $collection->addAttributeToSelect(['name', 'webrotate_path'])->addAttributeToFilter('webrotate_path', ['neq' => ''])->addAttributeToSort('entity_id', 'ASC');
	}

	/**
	 * [getpubStaticFileUrl This function is to get pub static path from root webrotate360 file. here _view folder is set instead of theme so replace it with theme name]
	 * [Change is done by NG]
	 */
	public function getpubStaticFileUrl($path) {

		$url = str_replace("_view", 'SIT/noctua', $this->_assetRepo->getUrl($path));
		return $url;
	}
}
