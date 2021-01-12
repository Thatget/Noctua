<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductReviewNew\Model\ProductReview\Attribute\Source;

class Country implements \Magento\Framework\Data\OptionSourceInterface {

	/**
	 * @var \Magento\Directory\Model\Country $country
	 */
	protected $_country;

	/**
	 * [__construct description]
	 * @param \Magento\Directory\Model\Country $country [description]
	 */
	public function __construct(
		\Magento\Directory\Model\Country $country
	) {
		$this->_country = $country;
	}

	/**
	 * @return array
	 */
	public function toOptionArray() {
		$countryCollection = $this->_country->getCollection();
		$options = [];
		foreach ($countryCollection as $country) {
			$options[] = [
				'value' => $country->getCountryId(),
				'label' => $country->getName(),
			];
		}
		return $options;
	}
}
