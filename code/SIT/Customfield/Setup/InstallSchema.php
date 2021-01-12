<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [13-06-2019]
 */
namespace SIT\Customfield\Setup;

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
        $table = $installer->getTable('catalog_eav_attribute');
        $columns = [
            'label_link' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Label Link',
            ],
        ];
        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($table, $name, $definition);
        }
        $installer->endSetup();
    }
}
