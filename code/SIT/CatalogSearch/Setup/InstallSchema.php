<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [17-05-2019]
 */

namespace SIT\CatalogSearch\Setup;

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
        /**
         * [$tableName cms_block]
         * @var [cms_block]
         */
        $tableName = $setup->getTable('cms_block');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'block_url_page' => [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Block Url Page',
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
