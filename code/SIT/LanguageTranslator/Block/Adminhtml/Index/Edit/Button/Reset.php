<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\LanguageTranslator\Block\Adminhtml\Index\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Reset implements ButtonProviderInterface {
	/**
	 * get button data
	 *
	 * @return array
	 */
	public function getButtonData() {
		return [
			'label' => __('Reset'),
			'class' => 'reset',
			'on_click' => 'location.reload();',
			'sort_order' => 30,
		];
	}
}
