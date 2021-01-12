<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use SIT\ProductTechNew\Setup\ProductTechnologySetup;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        ProductTechnologySetup::ENTITY_TYPE_CODE,
        'sit_producttechnew_eav_attribute',
        ProductTechnologySetup::ENTITY_TYPE_CODE . '_datetime',
        ProductTechnologySetup::ENTITY_TYPE_CODE . '_decimal',
        ProductTechnologySetup::ENTITY_TYPE_CODE . '_int',
        ProductTechnologySetup::ENTITY_TYPE_CODE . '_text',
        ProductTechnologySetup::ENTITY_TYPE_CODE . '_varchar',
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
