<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use SIT\ProductTechNew\Setup\EavTablesSetupFactory;
use SIT\ProductTechNew\Setup\ProductTechnologySetup;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var EavTablesSetupFactory
     */
    protected $eavTablesSetupFactory;

    /**
     * [__construct description]
     * @param EavTablesSetupFactory $eavTablesSetupFactory [description]
     */
    public function __construct(EavTablesSetupFactory $eavTablesSetupFactory)
    {
        $this->eavTablesSetupFactory = $eavTablesSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        $tableName = ProductTechnologySetup::ENTITY_TYPE_CODE;
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
                'sit_producttechnew_producttechnew',
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
                'sit_producttechnew_producttechnew',
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

        /** @var \SIT\ProductTechNew\Setup\EavTablesSetup $eavTablesSetup */
        $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
        $eavTablesSetup->createEavTables(ProductTechnologySetup::ENTITY_TYPE_CODE);

        if (!$installer->tableExists('sit_producttechnew_producttechnology_product')) {
            $tableName = $installer->getTable('sit_producttechnew_producttechnology_product');
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
                    'producttechnology_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Product Technology Id'
                )
                ->addIndex(
                    $installer->getIdxName('sit_producttechnew_producttechnology_product', ['rel_id']),
                    ['rel_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'sit_producttechnew_producttechnology_product',
                        'producttechnology_id',
                        ProductTechnologySetup::ENTITY_TYPE_CODE,
                        'entity_id'
                    ),
                    'producttechnology_id',
                    $installer->getTable(ProductTechnologySetup::ENTITY_TYPE_CODE),
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
                        'sit_producttechnew_producttechnology_product',
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

                ->setComment('SIT ProductTechnology Product')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}
