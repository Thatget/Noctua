<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use SIT\ProductReviewNew\Setup\EavTablesSetupFactory;
use SIT\ProductReviewNew\Setup\ProductReviewSetup;

class InstallSchema implements InstallSchemaInterface {
	/**
	 * @var EavTablesSetupFactory
	 */
	protected $eavTablesSetupFactory;

	/**
	 * [__construct description]
	 * @param EavTablesSetupFactory $eavTablesSetupFactory [description]
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
		$installer = $setup;
		$installer->startSetup();

		$tableName = ProductReviewSetup::ENTITY_TYPE_CODE;
		/**
		 * Create entity Table
		 */
		$table = $installer->getConnection()
			->newTable($installer->getTable($tableName))
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
			$installer->getIdxName($tableName, ['entity_type_id']),
			['entity_type_id']
		)->addForeignKey(
			$installer->getFkName(
				$tableName,
				'entity_type_id',
				'eav_entity_type',
				'entity_type_id'
			),
			'entity_type_id',
			$installer->getTable('eav_entity_type'),
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
			$installer->getIdxName($tableName, ['attribute_set_id']),
			['attribute_set_id']
		)->addForeignKey(
			$installer->getFkName(
				$tableName,
				'attribute_set_id',
				'eav_attribute_set',
				'attribute_set_id'
			),
			'attribute_set_id',
			$installer->getTable('eav_attribute_set'),
			'attribute_set_id',
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
			\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		);

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

		$installer->getConnection()->createTable($table);

		/** @var \SIT\ProductReviewNew\Setup\EavTablesSetup $eavTablesSetup */
		$eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
		$eavTablesSetup->createEavTables(ProductReviewSetup::ENTITY_TYPE_CODE);
		$tableName_Product = ProductReviewSetup::ENTITY_TYPE_CODE . '_product';
		if (!$installer->tableExists($tableName_Product)) {
			$tableName = $installer->getTable($tableName_Product);
			$table = $installer->getConnection()
				->newTable($tableName)
				->addColumn(
					'rel_id',
					Table::TYPE_INTEGER,
					10,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true,
					],
					'Relation Id'
				)
				->addColumn(
					'productreview_id',
					Table::TYPE_INTEGER,
					10,
					[
						'nullable' => false,
						'unsigned' => true,
					],
					'Product Review Id'
				)
				->addIndex(
					$installer->getIdxName($tableName_Product, ['rel_id']),
					['rel_id']
				)
				->addForeignKey(
					$installer->getFkName(
						$tableName_Product,
						'productreview_id',
						ProductReviewSetup::ENTITY_TYPE_CODE,
						'entity_id'
					),
					'productreview_id',
					$installer->getTable(ProductReviewSetup::ENTITY_TYPE_CODE),
					'entity_id',
					Table::ACTION_CASCADE
				)
				->addColumn(
					'product_id',
					Table::TYPE_INTEGER,
					null,
					[
						'nullable' => true,
						'unsigned' => true,
					],
					'Product Id'
				)
				->addForeignKey(
					$installer->getFkName(
						$tableName_Product,
						'product_id',
						'catalog_product_entity',
						'entity_id'
					),
					'product_id',
					$installer->getTable('catalog_product_entity'),
					'entity_id',
					Table::ACTION_CASCADE,
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

				->setComment('SIT ProductReview New Product')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}

		$tableName = $installer->getTable('sit_productawards_flat');
		if ($installer->getConnection()->isTableExists($tableName) != true) {
			$table = $installer->getConnection()
				->newTable($tableName)
				->addColumn(
					'entity_id',
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true,
					],
					'ID'
				)
				->addColumn(
					'product_id',
					Table::TYPE_SMALLINT,
					6,
					['nullable' => true, 'default' => ''],
					'Product ID'
				)
				->addColumn(
					'product_name',
					Table::TYPE_TEXT,
					255,
					['nullable' => true, 'default' => ''],
					'Product Name'
				)
				->addColumn(
					'review_website',
					Table::TYPE_TEXT,
					510,
					['nullable' => true, 'default' => ''],
					'Review Website'
				)
				->addColumn(
					'review_website_link',
					Table::TYPE_TEXT,
					510,
					['nullable' => true, 'default' => ''],
					'Review Website Link'
				)
				->addColumn(
					'review_image',
					Table::TYPE_TEXT,
					510,
					['nullable' => true, 'default' => ''],
					'Review Image'
				)
				->setComment('SIT Awards Table')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}
