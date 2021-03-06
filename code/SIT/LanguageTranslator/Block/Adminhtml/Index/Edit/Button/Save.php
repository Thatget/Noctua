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
use Magento\Ui\Component\Control\Container;

class Save extends Generic implements ButtonProviderInterface {
	/**
	 * get button data
	 *
	 * @return array
	 */
	public function getButtonData() {
		return [
			'label' => __('Save'),
			'class' => 'save primary',
			'data_attribute' => [
				'mage-init' => [
					'buttonAdapter' => [
						'actions' => [
							[
								'targetName' => 'languagetranslator_form.languagetranslator_form',
								'actionName' => 'save',
								'params' => [
									false,
								],
							],
						],
					],
				],
			],
			'class_name' => Container::SPLIT_BUTTON,
			'options' => $this->getOptions(),
		];
	}

	/**
	 * Retrieve options
	 *
	 * @return array
	 */
	protected function getOptions() {
		$options[] = [
			'id_hard' => 'save_and_new',
			'label' => __('Save & New'),
			'data_attribute' => [
				'mage-init' => [
					'buttonAdapter' => [
						'actions' => [
							[
								'targetName' => 'languagetranslator_form.languagetranslator_form',
								'actionName' => 'save',
								'params' => [
									true,
									[
										'back' => 'add',
									],
								],
							],
						],
					],
				],
			],
		];

		$options[] = [
			'id_hard' => 'save_and_close',
			'label' => __('Save & Close'),
			'data_attribute' => [
				'mage-init' => [
					'buttonAdapter' => [
						'actions' => [
							[
								'targetName' => 'languagetranslator_form.languagetranslator_form',
								'actionName' => 'save',
								'params' => [
									true,
								],
							],
						],
					],
				],
			],
		];

		return $options;
	}
}
