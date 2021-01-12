<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use SIT\ProductFaqNew\Setup\EavTablesSetupFactory;
use SIT\ProductFaqNew\Setup\ProductFaqSetup;

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

        $tableName = ProductFaqSetup::ENTITY_TYPE_CODE;
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
                'sit_productfaqnew_productfaq',
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
                'sit_productfaqnew_productfaq',
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

        $setup->getConnection()->createTable($table);

        /** @var \SIT\ProductFaqNew\Setup\EavTablesSetup $eavTablesSetup */
        $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
        $eavTablesSetup->createEavTables(ProductFaqSetup::ENTITY_TYPE_CODE);
        if (!$installer->tableExists($tableName . '_product')) {
            $tableName = $installer->getTable($tableName . '_product');
            $table = $installer->getConnection()
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
                    'productfaq_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Product FAQ Id'
                )
                ->addIndex(
                    $installer->getIdxName('sit_productfaqnew_productfaq_product', ['rel_id']),
                    ['rel_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'sit_productfaqnew_productfaq_product',
                        'productfaq_id',
                        ProductFaqSetup::ENTITY_TYPE_CODE,
                        'entity_id'
                    ),
                    'productfaq_id',
                    $installer->getTable(ProductFaqSetup::ENTITY_TYPE_CODE),
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
                    $installer->getFkName('sit_productfaqnew_productfaq_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
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

                ->setComment('SIT ProductFaqNew Product')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}
