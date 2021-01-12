<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Model\Config\Source;

use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

class GetCompatibilityList implements \Magento\Framework\Option\ArrayInterface {

	/**
	 * @var ProductCompHelper
	 */
	protected $prodCompHelper;

	/**
	 * [__construct description]
	 * @param ProductCompHelper $prodCompHelper [description]
	 */
	public function __construct(
		ProductCompHelper $prodCompHelper
	) {
		$this->prodCompHelper = $prodCompHelper;
	}

	/**
	 * Return options array
	 *
	 * @return array
	 */
	public function toOptionArray() {
		$getAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_VALUE);
		$getAttrId = $getAttrInfo->getAttributeId();
		$getAttrOptions = $this->prodCompHelper->getAttributeOptionAll($getAttrId);
		$options = [];
		$options[0]['value'] = '';
		$options[0]['label'] = '-- Please Select --';
		foreach ($getAttrOptions as $key => $value) {
			$options[] = ['label' => $value['value'], 'value' => $value['option_id']];
		}
		return $options;
	}
}
