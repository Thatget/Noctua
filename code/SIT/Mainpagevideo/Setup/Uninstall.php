<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Mainpagevideo\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use SIT\Mainpagevideo\Setup\MainpagevideoSetup;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        MainpagevideoSetup::ENTITY_TYPE_CODE,
        'sit_mainpagevideo_eav_attribute',
        MainpagevideoSetup::ENTITY_TYPE_CODE . '_datetime',
        MainpagevideoSetup::ENTITY_TYPE_CODE . '_decimal',
        MainpagevideoSetup::ENTITY_TYPE_CODE . '_int',
        MainpagevideoSetup::ENTITY_TYPE_CODE . '_text',
        MainpagevideoSetup::ENTITY_TYPE_CODE . '_varchar'
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
