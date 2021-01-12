<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Block\Adminhtml\GeneralTechnology\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ResetButton
 */
class ResetButton implements ButtonProviderInterface {
	/**
	 * @return array
	 */
	public function getButtonData() {
		return [
			'label' => __('Reset'),
			'class' => 'reset',
			'on_click' => 'location.reload();',
			'sort_order' => 20,
		];
	}
}
