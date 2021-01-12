<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\LanguageTranslator\Model\Source;

class Module implements \Magento\Framework\Option\ArrayInterface {

	/**
	 * @var \Magento\Framework\Module\ModuleList
	 */
	protected $moduleList;

	/**
	 * @param \Magento\Framework\Module\ModuleList $moduleList
	 */
	public function __construct(
		\Magento\Framework\Module\ModuleList $moduleList
	) {
		$this->moduleList = $moduleList;
	}

	/**
	 * Retrieve options array for all enabled modules.
	 *
	 * @return array
	 */
	public function toOptionArray() {

		$modules = [];
		$enabledModules = $this->moduleList->getAll();
		foreach ($enabledModules as $module) {
			$modules[] = ['value' => $module['name'], 'label' => $module['name']];
		}
		return $modules;
	}
}
