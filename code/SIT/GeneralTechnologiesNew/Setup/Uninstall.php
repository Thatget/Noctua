<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use SIT\GeneralTechnologiesNew\Setup\GeneralTechnologySetup;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        GeneralTechnologySetup::ENTITY_TYPE_CODE,
        'sit_generaltechnologiesnew_eav_attribute',
        GeneralTechnologySetup::ENTITY_TYPE_CODE . '_datetime',
        GeneralTechnologySetup::ENTITY_TYPE_CODE . '_decimal',
        GeneralTechnologySetup::ENTITY_TYPE_CODE . '_int',
        GeneralTechnologySetup::ENTITY_TYPE_CODE . '_text',
        GeneralTechnologySetup::ENTITY_TYPE_CODE . '_varchar',
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
