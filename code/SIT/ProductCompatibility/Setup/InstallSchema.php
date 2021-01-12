<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use SIT\ProductCompatibility\Setup\EavTablesSetupFactory;
use SIT\ProductCompatibility\Setup\ProductCompatibilitySetup;

class InstallSchema implements InstallSchemaInterface {
	/**
	 * @var EavTablesSetupFactory
	 */
	protected $eavTablesSetupFactory;

	/**
	 * Init
	 *
	 * @internal param EavTablesSetupFactory $EavTablesSetupFactory
	 */
	public function __construct(EavTablesSetupFactory $eavTablesSetupFactory) {
		$this->eavTablesSetupFactory = $eavTablesSetupFactory;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		$tableName = ProductCompatibilitySetup::ENTITY_TYPE_CODE;
		/**
		 * Create entity Table
		 */
		$table = $setup->getConnection()
			->newTable($setup->getTable($tableName))
			->addColumn(
				'entity_id',
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
				'Entity ID'
			)->setComment('Entity Table');

		$table->addColumn(
			'entity_type_id',
			Table::TYPE_SMALLINT,
			null,
			[
				'unsigned' => true,
				'nullable' => false,
				'default' => '0',
			],
			'Entity Type ID'
		)->addIndex(
			$setup->getIdxName($tableName, ['entity_type_id']),
			['entity_type_id']
		)->addForeignKey(
			$setup->getFkName(
				'sit_productcompatibility_productcompatibility',
				'entity_type_id',
				'eav_entity_type',
				'entity_type_id'
			),
			'entity_type_id',
			$setup->getTable('eav_entity_type'),
			'entity_type_id',
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		);

		$table->addColumn(
			'attribute_set_id',
			Table::TYPE_SMALLINT,
			null,
			[
				'unsigned' => true,
				'nullable' => false,
				'default' => '0',
			],
			'Attribute Set ID'
		)->addIndex(
			$setup->getIdxName($tableName, ['attribute_set_id']),
			['attribute_set_id']
		)->addForeignKey(
			$setup->getFkName(
				'sit_productcompatibility_productcompatibility',
				'attribute_set_id',
				'eav_attribute_set',
				'attribute_set_id'
			),
			'attribute_set_id',
			$setup->getTable('eav_attribute_set'),
			'attribute_set_id',
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		);

		// Add more static attributes here...

		$table->addColumn(
			'created_at',
			Table::TYPE_TIMESTAMP,
			null,
			['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
			'Creation Time'
		)->addColumn(
			'updated_at',
			Table::TYPE_TIMESTAMP,
			null,
			['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
			'Update Time'
		);

		$setup->getConnection()->createTable($table);

		/** @var \SIT\ProductCompatibility\Setup\EavTablesSetup $eavTablesSetup */
		$eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
		$eavTablesSetup->createEavTables(ProductCompatibilitySetup::ENTITY_TYPE_CODE);

		if (!$setup->tableExists($tableName . '_product')) {
			$tableName = $setup->getTable($tableName . '_product');
			$table = $setup->getConnection()
				->newTable($tableName)
				->addColumn(
					'rel_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true,
					],
					'Relation Id'
				)
				->addColumn(
					'productcompatibility_id',
					Table::TYPE_INTEGER,
					null,
					[
						'nullable' => false,
						'unsigned' => true,
					],
					'Product Compatibility Id'
				)
				->addIndex(
					$setup->getIdxName(ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_product', ['rel_id']),
					['rel_id']
				)
				->addForeignKey(
					$setup->getFkName(
						ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_product',
						'productcompatibility_id',
						ProductCompatibilitySetup::ENTITY_TYPE_CODE,
						'entity_id'
					),
					'productcompatibility_id',
					$setup->getTable(ProductCompatibilitySetup::ENTITY_TYPE_CODE),
					'entity_id',
					Table::ACTION_CASCADE
				)
				->addColumn(
					'product_id',
					Table::TYPE_INTEGER,
					null,
					[
						'nullable' => true,
						'default' => 0,
						'unsigned' => true,
					],
					'Product Id'
				)
				->addForeignKey(
					$setup->getFkName(ProductCompatibilitySetup::ENTITY_TYPE_CODE . '_product', 'product_id', 'catalog_product_entity', 'entity_id'),
					'product_id',
					$setup->getTable('catalog_product_entity'),
					'entity_id',
					Table::ACTION_CASCADE
				)
				->addColumn(
					'position',
					Table::TYPE_INTEGER,
					null,
					[
						'nullable' => true,
						'default' => 0,
					],
					'Position'
				)

				->setComment('SIT ProductCompatibility Product')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$setup->getConnection()->createTable($table);
		}

		$setup->endSetup();
	}
}
