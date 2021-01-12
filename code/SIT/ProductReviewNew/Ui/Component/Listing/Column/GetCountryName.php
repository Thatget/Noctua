<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

/**
 * Return country name in grid
 */
namespace SIT\ProductReviewNew\Ui\Component\Listing\Column;

class GetCountryName extends \Magento\Ui\Component\Listing\Columns\Column {
	/**
	 * @var \Magento\Directory\Model\CountryFactory
	 */
	protected $_countryFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Directory\Model\CountryFactory                      $countryFactory     [description]
	 * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            [description]
	 * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory [description]
	 * @param array                                                        $components         [description]
	 * @param array                                                        $data               [description]
	 */
	public function __construct(
		\Magento\Directory\Model\CountryFactory $countryFactory,
		\Magento\Framework\View\Element\UiComponent\ContextInterface $context,
		\Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = []
	) {
		$this->_countryFactory = $countryFactory;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as $key => $value) {
				$country = $this->_countryFactory->create()->loadByCode($value['review_country']);
				$dataSource['data']['items'][$key]['review_country'] = $country->getName();

			}
		}
		return $dataSource;
	}
}
