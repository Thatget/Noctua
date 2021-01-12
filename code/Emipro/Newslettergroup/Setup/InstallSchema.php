<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Setup;

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

        $tableName = $installer->getTable('emipro_newsletter_user_group');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
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
                    'id'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'title'
                )
                ->addColumn(
                    'store_view',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Store View'
                )
                ->addColumn(
                    'customer_groups',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Customer Groups'
                )
                ->addColumn(
                    'notify_email',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Notify Email'
                )
                ->addColumn(
                    'source_website',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Source Website'
                )
                ->addColumn(
                    'display_on_front',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true, 'default' => null],
                    'Display on Frontend'
                )
                ->addColumn(
                    'creation_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Creation Time'
                )
                ->addColumn(
                    'update_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Update Time'
                )
                ->addColumn(
                    'customers',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => null,
                    ],
                    'Customers'
                )
                ->addColumn(
                    'group_ids',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'group_ids'
                )
                ->setComment('Emipro Newsletter User Group')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();

        $installer->startSetup();

        $tableName = $installer->getTable('emipro_newsletter_user_subscriber');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
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
                    'id'
                )
                ->addColumn(
                    'group_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'group_id'
                )
                ->addColumn(
                    'sub_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'sub_id'
                )
                ->addColumn(
                    'update_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Update Time'
                )
                ->setComment('Zeon Job Applications')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();

        $installer->startSetup();

        $eavTable = $installer->getTable('newsletter_queue');

        $columns = [
            'user_group' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'User Group',
                'default' => 0,
            ],

        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($eavTable, $name, $definition);
        }

        $installer->endSetup();

        $installer->startSetup();

        $eavTable = $installer->getTable('newsletter_queue_link');

        $columns = [
            'group_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Group Id',
            ],

        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($eavTable, $name, $definition);
        }

        $installer->endSetup();

        $installer->startSetup();

        $eavTable = $installer->getTable('newsletter_subscriber');

        $columns = [
            'subscriber_country' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 30,
                'nullable' => true,
                'default' => null,
                'comment' => 'Subscriber Country',
            ],
            'subscriber_state' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 30,
                'nullable' => true,
                'default' => null,
                'comment' => 'Subscriber State',
            ],
            'source_website' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Source Website',
            ],
            'is_bounced' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is Bounced',
            ],
            'newsletter_group_id' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => false,
                'default' => null,
                'comment' => 'Newsletter Group Id',
            ],
            'subscription_date' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'length' => null,
                'nullable' => true,
                'default' => null,
                'comment' => 'Subscription Date',
            ],
            'position' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Position',
            ],
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($eavTable, $name, $definition);
        }

        $installer->endSetup();

    }
}
