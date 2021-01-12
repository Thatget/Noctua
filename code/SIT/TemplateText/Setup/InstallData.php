<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\TemplateText\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\TemplateText\Setup\TemplateTextSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * TemplateText setup factory
     *
     * @var TemplateTextSetupFactory
     */
    protected $templatetextSetupFactory;

    /**
     * [__construct description]
     * @param TemplateTextSetupFactory $templatetextSetupFactory [description]
     */
    public function __construct(TemplateTextSetupFactory $templatetextSetupFactory)
    {
        $this->templatetextSetupFactory = $templatetextSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var TemplateTextSetup $templatetextSetup */
        $templatetextSetup = $this->templatetextSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $templatetextSetup->installEntities();
        $entities = $templatetextSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $templatetextSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
