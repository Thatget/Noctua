<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductVideoNew\Block\ProductVideo;

class ProductVideoView extends \Magento\Framework\View\Element\Template
{

	/**
	 * @var \SIT\ProductVideoNew\Model\ProductVideoFactory
	 */
	protected $productVideoFactory;

	/**
	 * @var \SIT\ProductVideoNew\Model\ProductFactory
	 */
	protected $sitProductFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var  \Magento\Framework\Json\Encoder
	 */
	protected $jsonEncoder;

	/**
	 * @var \SIT\MainAdmin\Helper\Data
	 */
	protected $sitHelper;

	/**
	 * @var \Magento\Eav\Model\Config
	 */
	protected $eavConfig;
	protected $countryCode;

	/**
	 * sit product table name for join product name
	 */
	const SIT_PRODUCTVIDEO_PRODUCT_TABLE = 'sit_productvideonew_productvideo_product';

	/**
	 * category product table name for join product name
	 */
	const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

	/**
	 * product name attribute ID
	 */
	const PRODUCT_NAME_ATTRIBUTE_ID = '71';

	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\Template\Context                        $context             [description]
	 * @param \SIT\ProductVideoNew\Model\ResourceModel\ProductVideo\CollectionFactory $productVideoFactory [description]
	 * @param \SIT\ProductVideoNew\Model\ProductFactory                               $sitProductFactory   [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                              $storeManager        [description]
	 * @param \Magento\Framework\Json\Encoder                                         $jsonEncoder         [description]
	 * @param \SIT\MainAdmin\Helper\Data                                              $sitHelper           [description]
	 * @param \Magento\Eav\Model\Config                                               $eavConfig           [description]
	 * @param array                                                                   $data                [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\SIT\ProductVideoNew\Model\ResourceModel\ProductVideo\CollectionFactory $productVideoFactory,
		\SIT\ProductVideoNew\Model\ProductFactory $sitProductFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Json\Encoder $jsonEncoder,
		\SIT\MainAdmin\Helper\Data $sitHelper,
		\Magento\Eav\Model\Config $eavConfig,
		array $data = []
	) {
		$this->productVideoFactory = $productVideoFactory;
		$this->sitProductFactory = $sitProductFactory;
		$this->storeManager = $storeManager;
		$this->jsonEncoder = $jsonEncoder;
		$this->sitHelper = $sitHelper;
		$this->eavConfig = $eavConfig;
		if (isset($_SERVER['GEOIP_COUNTRY_CODE'])) {
			$this->countryCode = strtolower($_SERVER['GEOIP_COUNTRY_CODE']);
		}
		parent::__construct($context, $data);
	}

	/**
	 * Return product video array usign product id
	 *
	 * @param  int $currentProID
	 * @return array
	 */
	public function getProductVideoData($currentProID, $filter)
	{
		$products = $this->sitProductFactory->create()->getCollection()
			->addFieldToSelect(['productvideo_id', 'product_id'])
			->addFieldToFilter("product_id", ["eq" => $currentProID]);

		$proVideoId_Arr = [];
		foreach ($products as $key => $value) {
			$proVideoId_Arr[] = $value['productvideo_id'];
		}
		$videos = $this->productVideoFactory->create()
			->setStoreId($this->getCurrentStoreId())
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', ['eq' => 1])
			->addAttributeToFilter('video_reviewer_url', [$filter => true])
			->addAttributeToFilter('entity_id', ['in' => $proVideoId_Arr])
			->setOrder('video_title', "ASC");
		$video_array = [];

		$tempCount = 0;
		foreach ($videos as $key => $value) {
			$video_array[$tempCount]['video_title'] = $value->getVideoTitle();
			$video_array[$tempCount]['video_link'] = $this->getVideoLink($value);
			$video_array[$tempCount]['video_thumbnail'] = $this->getVideoThumbnail($value);
			$video_array[$tempCount]['video_reviewer_url'] = $this->getVideoReviewerUrl($value);
			if ($this->sitHelper->getFlagCode($this->getLangOptions($value->getVideoLanguage())) == '') {
				$video_array[$tempCount]['productvideo_language'] = '';
				$video_array[$tempCount]['productvideo_lang_name'] = null;
			} else {
				$video_array[$tempCount]['productvideo_language'] = $this->sitHelper->getImage('flags/', $this->sitHelper->getFlagCode($this->getLangOptions($value->getVideoLanguage())));
				$video_array[$tempCount]['productvideo_lang_name'] = trim($this->getLangOptions($value->getVideoLanguage()));
			}
			if ($value->getVideoReviewerUrl() != '') {
				$video_array[$tempCount]['arrow_img'] = $this->sitHelper->getImage('default/images/', 'duble-arrow-gray.png');
			} else {
				$video_array[$tempCount]['arrow_img'] = '';
			}
			$tempCount++;
		}
		return $video_array;
	}
	public function getVideoThumbnail($value)
	{
		// if($value->getVideoReviewerUrlCn() && ((!$this->countryCode && $this->getCurrentCountryCode() == 'cn' ) || $this->countryCode == 'cn'))
		if ($value->getVideoThumbnailCn() && $this->countryCode == 'cn') {
			return $value->getVideoThumbnailCn();
		}
		return $this->getYouTubeThumbnail($value->getVideoLink());
	}
	public function getVideoReviewerUrl($value)
	{
		// if($value->getVideoReviewerUrlCn() && ((!$this->countryCode && $this->getCurrentCountryCode() == 'cn' ) || $this->countryCode == 'cn'))
		if ($value->getVideoReviewerUrlCn() && $this->countryCode == 'cn') {
			return $value->getVideoReviewerUrlCn();
		}
		return $value->getVideoReviewerUrl();
	}
	public function getVideoLink($value)
	{
		// if($value->getVideoLinkCn() && ((!$this->countryCode && $this->getCurrentCountryCode() == 'cn' ) || $this->countryCode == 'cn') )
		if ($value->getVideoLinkCn() && $this->countryCode == 'cn') {
			return $value->getVideoLinkCn();
		}
		return $this->getYouTubeEmbed($value->getVideoLink());
	}
	public function getCurrentCountryCode()
	{
		$localeCode =  $this->sitHelper->getLocaleCode($this->getCurrentStoreId());
		$countryCode = explode('_', $localeCode);
		$countryCode = $countryCode[0];
		return $countryCode;
	}
	/**
	 * Return collection to check product video is available or not
	 *
	 * @param  [type] $currentProID [description]
	 * @return \SIT\ProductVideoNew\Model\ProductVideoFactory
	 */
	public function getProductVideoIds($currentProID)
	{
		$products = $this->sitProductFactory->create()->getCollection()
			->addFieldToSelect(['productvideo_id', 'product_id'])
			->addFieldToFilter("product_id", ["eq" => $currentProID]);

		$proVideoId_Arr = [];
		foreach ($products as $key => $value) {
			$proVideoId_Arr[] = $value['productvideo_id'];
		}
		$videos = $this->productVideoFactory->create()
			->setStoreId($this->getCurrentStoreId())
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', ['eq' => 1])
			->addAttributeToFilter('entity_id', ['in' => $proVideoId_Arr]);
		return $videos;
	}
	/**
	 * Return Current Store ID
	 *
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	protected function getCurrentStoreId()
	{
		return $this->storeManager->getStore()->getId();
	}

	/**
	 * Get language label by attribute code
	 *
	 * @param  [type] $attrCode [description]
	 * @return [type]           [description]
	 */
	protected function getLangOptions($attrCode)
	{
		$attribute = $this->eavConfig->getAttribute('sit_productvideonew_productvideo', 'video_language');
		$language = $attribute->getSource()->getAllOptions();
		$attr_label = '';
		foreach ($language as $key => $value) {
			if ($value['value'] == $attrCode) {
				$attr_label = $value['label'];
			}
		}
		return $attr_label;
	}

	/**
	 * Convert youtube embed video
	 *
	 * @param  [type] $videoLink [description]
	 * @return [type]            [description]
	 */
	protected function getYouTubeEmbed($videoLink)
	{
		if (strstr($videoLink, 'youtube')) {
			$videoLink = explode('/', $videoLink);
			$embedKey = explode('?', $videoLink[4]);
			return 'https://www.youtube.com/embed/' . $embedKey[0];
		} else if (strstr($videoLink, 'ixigua')) {
			$videoLink = explode('/', $videoLink);
			if ($videoLink[3])
				return 'https://www.ixigua.com/embed?group_id=' . str_replace('i', '', $videoLink[3]);
			else
				return $videoLink;
		} else {
			return $videoLink;
		}
	}

	/**
	 * Convert youtube thumbnail
	 *
	 * @param  [type] $videoLink [description]
	 * @return [type]            [description]
	 */
	protected function getYouTubeThumbnail($videoLink)
	{
		if (strstr($videoLink, 'youtube')) {
			$videoLink = explode('/', $videoLink);
			$embedKey = explode('?', $videoLink[4]);
			return 'https://img.youtube.com/vi/' . $embedKey[0] . '/mqdefault.jpg';
		} else {
			return $videoLink;
		}
	}
}
