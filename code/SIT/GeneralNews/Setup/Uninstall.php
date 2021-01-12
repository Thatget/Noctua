<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        NewsSetup::ENTITY_TYPE_CODE,
        NewsSetup::EAV_ENTITY_TYPE_CODE . '_eav_attribute',
        NewsSetup::ENTITY_TYPE_CODE . '_datetime',
        NewsSetup::ENTITY_TYPE_CODE . '_decimal',
        NewsSetup::ENTITY_TYPE_CODE . '_int',
        NewsSetup::ENTITY_TYPE_CODE . '_text',
        NewsSetup::ENTITY_TYPE_CODE . '_varchar',
    ];

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context) //@codingStandardsIgnoreLine

    {
        $setup->startSetup();

        foreach ($this->tablesToUninstall as $table) {
            if ($setup->tableExists($table)) {
                $setup->getConnection()->dropTable($setup->getTable($table));
            }
        }

        $setup->endSetup();
    }
}
