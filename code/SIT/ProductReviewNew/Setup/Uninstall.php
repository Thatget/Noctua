<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use SIT\ProductReviewNew\Setup\ProductReviewSetup;

class Uninstall implements UninstallInterface {
	/**
	 * @var array
	 */
	protected $tablesToUninstall = [
		ProductReviewSetup::ENTITY_TYPE_CODE,
		ProductReviewSetup::EAV_ENTITY_TYPE_CODE,
		ProductReviewSetup::ENTITY_TYPE_CODE . '_datetime',
		ProductReviewSetup::ENTITY_TYPE_CODE . '_decimal',
		ProductReviewSetup::ENTITY_TYPE_CODE . '_int',
		ProductReviewSetup::ENTITY_TYPE_CODE . '_text',
		ProductReviewSetup::ENTITY_TYPE_CODE . '_varchar',
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
