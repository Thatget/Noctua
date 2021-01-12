<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace Qsoft\LanguageTranslatorCustom\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {
	// @codingStandardsIgnoreStart
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	// @codingStandardsIgnoreEnd
	{
		$installer = $setup;
		$installer->startSetup();
		$connection = $installer->getConnection();
        $connection->addIndex(
            $installer->getTable('sit_translation'),
            $installer->getIdxName('sit_translation', ['original_string']),
            ['original_string']
        );
        $connection->addIndex(
            $installer->getTable('sit_translation'),
            $installer->getIdxName(
                'sit_translation',
                [
                    'original_string', 'translation_id'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [
                'original_string'
            ],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
        );
		$installer->endSetup();
	}
}
