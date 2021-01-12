<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */

namespace Mediarocks\ProSlider\Model\Option;

use \Magento\Framework\Option\ArrayInterface;

class PageList implements ArrayInterface {

	/**
	 * @return [array] [page-type]
	 */
	public function toOptionArray() {
		return [
			['value' => '', 'label' => __('---Select---')],
			['value' => 'CMS', 'label' => __('CMS Page')],
			['value' => 'Category', 'label' => __('Category Page')],
			['value' => 'Product', 'label' => __('Product Page')],
			['value' => 'Custom', 'label' => __('Custom Page')],
		];
	}
}