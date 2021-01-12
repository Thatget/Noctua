<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Model;

class ReviewLangOptions implements \Magento\Framework\Data\OptionSourceInterface {

	/**
	 * @var \Magento\Eav\Model\Config
	 */
	protected $_eavConfig;

	/**
	 * [__construct description]
	 * @param \Magento\Eav\Model\Config $eavConfig [description]
	 */
	public function __construct(
		\Magento\Eav\Model\Config $eavConfig
	) {
		$this->eavConfig = $eavConfig;
	}

	/**
	 * @return array
	 */
	public function toOptionArray() {
		$attribute = $this->eavConfig->getAttribute(\SIT\ProductReviewNew\Setup\ProductReviewSetup::ENTITY_TYPE_CODE, 'r_lng');
		$langOptions = $attribute->getSource()->getAllOptions();
		$options = [];
		foreach ($langOptions as $key => $value) {
			if ($value['value'] != '') {
				$options[] = [
					'value' => $value['value'],
					'label' => $value['label'],
				];
			}
		}
		return $options;
	}
}