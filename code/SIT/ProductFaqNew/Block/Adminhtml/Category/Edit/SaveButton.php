<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Block\Adminhtml\Category\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 */
class SaveButton implements ButtonProviderInterface {

	/**
	 * @return array
	 */
	public function getButtonData() {
		$data = [
			'label' => __('Save FAQ Category'),
			'class' => 'save primary',
			'data_attribute' => [
				'mage-init' => ['button' => ['event' => 'save']],
				'form-role' => 'save',
			],
			'sort_order' => 90,
		];
		return $data;
	}
}
