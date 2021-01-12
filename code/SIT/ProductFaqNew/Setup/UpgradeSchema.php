<?php

namespace SIT\ProductFaqNew\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;
		$installer->startSetup();
		if (version_compare($context->getVersion(), '1.0.3', '<')) {
			if (!$installer->tableExists('sit_productfaqnew_category')) {
				$tableName = $installer->getTable('sit_productfaqnew_category');
				$table = $installer->getConnection()
					->newTable($tableName)
					->addColumn(
						'id',
						Table::TYPE_INTEGER,
						10,
						[
							'identity' => true,
							'unsigned' => true,
							'nullable' => false,
							'primary' => true,
						],
						'Id'
					)
					->addColumn(
						'parent_cat_name',
						Table::TYPE_TEXT,
						510,
						[
							'nullable' => true,
							'default' => null,
						],
						'Parent Category Name'
					)
					->addColumn(
						'cat_name',
						Table::TYPE_TEXT,
						510,
						[
							'nullable' => true,
							'default' => null,
						],
						'Child Category Name'
					)
					->addColumn(
						'status',
						Table::TYPE_SMALLINT,
						6,
						['nullable' => true, 'default' => '0'],
						'Status'
					)
					->addColumn(
						'position',
						Table::TYPE_SMALLINT,
						6,
						['nullable' => true, 'default' => '0'],
						'Position'
					)
					->addColumn(
						'created_at',
						Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
						'Creation Time'
					)
					->addColumn(
						'updated_at',
						Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
						'Update Time'
					)
					->setComment('SIT ProductFaqNew Category Table')
					->setOption('type', 'InnoDB')
					->setOption('charset', 'utf8');
				$installer->getConnection()->createTable($table);
			}
		}

		$installer->endSetup();

	}
}