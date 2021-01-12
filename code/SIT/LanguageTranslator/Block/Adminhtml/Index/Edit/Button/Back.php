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

class Back extends Generic implements ButtonProviderInterface {
	/**
	 * get button data
	 *
	 * @return array
	 */
	public function getButtonData() {
		return [
			'label' => __('Back'),
			'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
			'class' => 'back',
			'sort_order' => 10,
		];
	}

	/**
	 * Get URL for back (reset) button
	 *
	 * @return string
	 */
	public function getBackUrl() {
		return $this->getUrl('*/*/');
	}
}
