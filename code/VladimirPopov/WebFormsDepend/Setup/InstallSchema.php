<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [25-04-2019]
 */

namespace VladimirPopov\WebFormsDepend\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /**
         * [$tableName webforms]
         * @var [webforms]
         */
        $tableName = $setup->getTable('webforms');

        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'footer_description' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Footer Description',
                ],
                'source_group' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Source Group',
                ],
                'source_website' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Source Website',
                ],
                'use_in_soap' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'Use In Soap',
                ],
                'use_express_delivery' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Use Express Delivery',
                ],
                'registered_only' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'nullable' => false,
                    'comment' => 'Registered Only',
                ],
                'email_smtpvalidation' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 1,
                    'nullable' => false,
                    'comment' => 'Email Smtpvalidation',
                ],
            ];

            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
        $installer->endSetup();
    }
}
