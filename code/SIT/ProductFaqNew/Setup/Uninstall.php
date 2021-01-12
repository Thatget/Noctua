<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use SIT\ProductFaqNew\Setup\ProductFaqSetup;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        ProductFaqSetup::ENTITY_TYPE_CODE,
        ProductFaqSetup::EAV_ENTITY_TYPE_CODE . '_eav_attribute',
        ProductFaqSetup::ENTITY_TYPE_CODE . '_datetime',
        ProductFaqSetup::ENTITY_TYPE_CODE . '_decimal',
        ProductFaqSetup::ENTITY_TYPE_CODE . '_int',
        ProductFaqSetup::ENTITY_TYPE_CODE . '_text',
        ProductFaqSetup::ENTITY_TYPE_CODE . '_varchar',
    ];

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
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
