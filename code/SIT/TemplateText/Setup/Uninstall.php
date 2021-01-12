<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\TemplateText\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use SIT\TemplateText\Setup\TemplateTextSetup;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        TemplateTextSetup::ENTITY_TYPE_CODE,
        'sit_templatetext_eav_attribute',
        TemplateTextSetup::ENTITY_TYPE_CODE . '_datetime',
        TemplateTextSetup::ENTITY_TYPE_CODE . '_decimal',
        TemplateTextSetup::ENTITY_TYPE_CODE . '_int',
        TemplateTextSetup::ENTITY_TYPE_CODE . '_text',
        TemplateTextSetup::ENTITY_TYPE_CODE . '_varchar',
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
