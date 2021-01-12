<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\LanguageTranslator\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {
	// @codingStandardsIgnoreStart
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	// @codingStandardsIgnoreEnd
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('sit_translation')) {
			$tableName = $installer->getTable('sit_translation');
			$table = $installer->getConnection()
				->newTable($tableName)
				->addColumn(
					'translation_id',
					Table::TYPE_INTEGER,
					10,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true,
					],
					'Translation Id'
				)
				->addColumn(
					'original_string',
					Table::TYPE_TEXT,
					1020,
					[
						'nullable' => true,
						'default' => null,
					],
					'Original String'
				)
				->addColumn(
					'interface',
					Table::TYPE_TEXT,
					255,
					[
						'nullable' => true,
						'default' => null,
					],
					'Interface'
				)
				->addColumn(
					'status',
					Table::TYPE_SMALLINT,
					null,
					[
						'nullable' => true,
						'default' => null,
					],
					'Status'
				)
				->addIndex(
					$installer->getIdxName('sit_translation', ['translation_id']),
					['translation_id']
				)
				->setComment('SIT LanguageTranslator')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
			$table = $installer->getConnection()
				->newTable($installer->getTable('sit_storewise_translation'))
				->addColumn(
					'id',
					Table::TYPE_INTEGER, null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true,
					],
					'ID'
				)
				->addColumn(
					'translation_id',
					Table::TYPE_INTEGER, null,
					[
						'unsigned' => true,
						'nullable' => false,
						'default' => '0',
					],
					'Translation id'
				)
				->addColumn(
					'translated_string',
					Table::TYPE_TEXT,
					1020,
					[
						'nullable' => false,
					],
					'translated string'
				)
				->addColumn(
					'store_id',
					Table::TYPE_INTEGER, null,
					[
						'unsigned' => true,
						'nullable' => false,
						'default' => '0',
					],
					'store_id'
				)
				->addIndex(
					$installer->getIdxName('sit_storewise_translation', ['translation_id']),
					['translation_id']
				)
				->addForeignKey(
					$installer->getFkName(
						'sit_storewise_translation',
						'translation_id',
						'sit_translation',
						'translation_id'
					),
					'translation_id',
					$installer->getTable('sit_translation'),
					'translation_id',
					\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
				)
				->setComment('Sit Storewise Translation');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}
