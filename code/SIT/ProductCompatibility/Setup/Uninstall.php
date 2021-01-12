<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use SIT\ProductCompatibility\Setup\ProductCompatibilitySetup;

class Uninstall implements UninstallInterface {
	/**
	 * @var array
	 */
	protected $tablesToUninstall = [
		ProductCompatibilitySetup::ENTITY_TYPE_CODE,
		'sit_productcompatibility_eav_attribute',
		ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_datetime',
		ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_decimal',
		ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_int',
		ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_text',
		ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_varchar',
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
