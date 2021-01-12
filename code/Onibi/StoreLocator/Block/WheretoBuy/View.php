<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace Onibi\StoreLocator\Block\WheretoBuy;

class View extends \Magento\Framework\View\Element\Template {

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \Magento\Cms\Model\BlockRepository
	 */
	protected $staticBlockRepository;

	/**
	 * @var \SIT\MainAdmin\Helper\Data
	 */
	protected $sitHelper;

	/**
	 * @var \Magento\Directory\Model\CountryFactory
	 */
	protected $countryFactory;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context               [description]
	 * @param \Magento\Store\Model\StoreManagerInterface       $storeManager          [description]
	 * @param \Magento\Cms\Model\BlockRepository               $staticBlockRepository [description]
	 * @param \SIT\MainAdmin\Helper\Data                       $sitHelper             [description]
	 * @param \Magento\Directory\Model\CountryFactory          $countryFactory        [description]
	 * @param array                                            $data                  [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Cms\Model\BlockRepository $staticBlockRepository,
		\SIT\MainAdmin\Helper\Data $sitHelper,
		\Magento\Directory\Model\CountryFactory $countryFactory,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->storeManager = $storeManager;
		$this->staticBlockRepository = $staticBlockRepository;
		$this->sitHelper = $sitHelper;
		$this->countryFactory = $countryFactory;
	}

	/**
	 * Get media url for image
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	public function getMediaUrl() {
		return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}

	/**
	 * Get static block content
	 * @return [type] [description]
	 */
	public function getStaticContent() {
		$staticBlock = $this->staticBlockRepository->getById('wheretobuy_pretext');
		if ($staticBlock && $staticBlock->isActive()) {
			return $this->sitHelper->getCmsFilterContent($staticBlock->getContent());
		} else {
			return false;
		}
	}

	/**
	 * Get country name
	 * @return \Magento\Directory\Model\CountryFactory
	 */
	public function getCountryName($code) {
		return $this->countryFactory->create()->load($code)->getName();
	}
}