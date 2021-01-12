<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use SIT\ProductVideoNew\Setup\ProductVideoSetup;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        ProductVideoSetup::ENTITY_TYPE_CODE,
        ProductVideoSetup::EAV_ENTITY_TYPE_CODE . '_eav_attribute',
        ProductVideoSetup::ENTITY_TYPE_CODE . '_datetime',
        ProductVideoSetup::ENTITY_TYPE_CODE . '_decimal',
        ProductVideoSetup::ENTITY_TYPE_CODE . '_int',
        ProductVideoSetup::ENTITY_TYPE_CODE . '_text',
        ProductVideoSetup::ENTITY_TYPE_CODE . '_varchar',
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
