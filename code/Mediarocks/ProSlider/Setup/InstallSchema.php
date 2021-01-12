<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mediarocks\ProSlider\Setup\ProsliderSliderSetup;
use Mediarocks\ProSlider\Setup\ProsliderSlideSetup;
use Mediarocks\ProSlider\Setup\SlideEavTablesSetupFactory;
use Mediarocks\ProSlider\Setup\SliderEavTablesSetupFactory;

class InstallSchema implements InstallSchemaInterface {
	/**
	 * @var EavTablesSetupFactory
	 */
	protected $eavTablesSetupFactory;

	/**
	 * [__construct description]
	 * @param SliderEavTablesSetupFactory $eavTablesSetupFactory   [description]
	 * @param SlideEavTablesSetupFactory  $slideTablesSetupFactory [description]
	 */
	public function __construct(
		SliderEavTablesSetupFactory $eavTablesSetupFactory,
		SlideEavTablesSetupFactory $slideTablesSetupFactory
	) {
		$this->eavTablesSetupFactory = $eavTablesSetupFactory;
		$this->slideTablesSetupFactory = $slideTablesSetupFactory;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		$tableName = ProsliderSliderSetup::ENTITY_TYPE_CODE;
		$slide_tableName = ProsliderSlideSetup::ENTITY_TYPE_CODE_SL;

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
				$tableName,
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
				$tableName,
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
		/** @var \SIT\Mainpagevideo\Setup\EavTablesSetup $eavTablesSetup */
		$eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
		$eavTablesSetup->createEavTables(ProsliderSliderSetup::ENTITY_TYPE_CODE);
		$setup->getConnection()->createTable($table);

		/* Added By MJ [13.03.2019]*/
		$slide_table = $setup->getConnection()
			->newTable($setup->getTable($slide_tableName))
			->addColumn(
				'entity_id',
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
				'Entity ID'
			)->setComment('Entity Table');

		$slide_table->addColumn(
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
			$setup->getIdxName($slide_tableName, ['entity_type_id']),
			['entity_type_id']
		)->addForeignKey(
			$setup->getFkName(
				$slide_tableName,
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

		$slide_table->addColumn(
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
			$setup->getIdxName($slide_tableName, ['attribute_set_id']),
			['attribute_set_id']
		)->addForeignKey(
			$setup->getFkName(
				$slide_tableName,
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

		$slide_table->addColumn(
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

		$slideTablesSetup = $this->slideTablesSetupFactory->create(['setup' => $setup]);
		$slideTablesSetup->createSlideEavTables(ProsliderSlideSetup::ENTITY_TYPE_CODE_SL);
		$setup->getConnection()->createTable($slide_table);
		$setup->endSetup();
		/* End By MJ [13.03.2019]*/
	}
}
