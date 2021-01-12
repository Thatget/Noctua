<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace SIT\WebformApi\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('sit_country_price')) {
            $tableName = $installer->getTable('sit_country_price');
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
                    'country_id',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Country Id'
                )
                ->addColumn(
                    'delivery_type',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Delivery Type'
                )
                ->addColumn(
                    'price',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Price'
                )
                ->addColumn(
                    'ship_icon',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Ship Icon'
                )
                ->addColumn(
                    'delivery_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Delivery Id'
                )
                ->addColumn(
                    'delivery_duration',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Delivery Duration'
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
                    $installer->getIdxName('sit_country_price', ['id']),
                    ['id']
                )
                ->setComment('SIT WebformApi')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
