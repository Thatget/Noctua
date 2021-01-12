<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface {
	/**
	 * @var array
	 */
	protected $tablesToUninstall = [
		ProSliderSliderSetup::ENTITY_TYPE_CODE,
		ProSliderSliderSetup::EAV_ENTITY_TYPE_CODE . '_eav_attribute',
		ProSliderSliderSetup::ENTITY_TYPE_CODE . '_datetime',
		ProSliderSliderSetup::ENTITY_TYPE_CODE . '_int',
		ProSliderSliderSetup::ENTITY_TYPE_CODE . '_text',
		ProSliderSliderSetup::ENTITY_TYPE_CODE . '_varchar',
		ProSliderSlideSetup::ENTITY_TYPE_CODE_SL,
		ProSliderSlideSetup::EAV_ENTITY_TYPE_CODE_SL . '_eav_attribute',
		ProSliderSlideSetup::ENTITY_TYPE_CODE_SL . '_datetime',
		ProSliderSlideSetup::ENTITY_TYPE_CODE_SL . '_int',
		ProSliderSlideSetup::ENTITY_TYPE_CODE_SL . '_text',
		ProSliderSlideSetup::ENTITY_TYPE_CODE_SL . '_varchar',
	];

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		foreach ($this->tablesToUninstall as $table) {
			if ($setup->tableExists($table)) {
				$setup->getConnection()->dropTable($setup->getTable($table));
			}
		}

		$setup->endSetup();
	}
}
