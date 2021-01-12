<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Setup;

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
        if (!$installer->tableExists('onibi_storelocator')) {
            $tableName = $installer->getTable('onibi_storelocator');
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Entity Id'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Name'
                )
                ->addColumn(
                    'address',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Address'
                )
                ->addColumn(
                    'zipcode',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Zipcode'
                )
                ->addColumn(
                    'city',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'City'
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
                    'phone',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Phone'
                )
                ->addColumn(
                    'fax',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Fax'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Description'
                )
                ->addColumn(
                    'store_href',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Store Href'
                )
                ->addColumn(
                    'store_url',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Store Url'
                )
                ->addColumn(
                    'image',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Image'
                )
                ->addColumn(
                    'marker',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Marker'
                )
                ->addColumn(
                    'lat',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Lat'
                )
                ->addColumn(
                    'long',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Long'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Type'
                )
                ->addColumn(
                    'created_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => true,
                        'default' => Table::TIMESTAMP_INIT,
                    ],
                    'Created Time'
                )
                ->addColumn(
                    'update_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => true,
                        'default' => Table::TIMESTAMP_INIT_UPDATE,
                    ],
                    'Update Time'
                )
                ->addColumn(
                    'store_email',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Store Email'
                )
                ->addColumn(
                    'province',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Province'
                )
                ->addColumn(
                    'mobile_no',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Mobile No'
                )
                ->addColumn(
                    'qq_no',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Qq No'
                )
                ->addColumn(
                    'contact_person',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Contact Person'
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
                    $installer->getIdxName('onibi_storelocator', ['entity_id']),
                    ['entity_id']
                )
                ->setComment('Onibi StoreLocator')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
