<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\GeneralNews\Setup\NewsSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * News setup factory
     *
     * @var NewsSetupFactory
     */
    protected $newsSetupFactory;

    /**
     * [__construct description]
     * @param NewsSetupFactory $newsSetupFactory [description]
     */
    public function __construct(NewsSetupFactory $newsSetupFactory)
    {
        $this->newsSetupFactory = $newsSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var NewsSetup $newsSetup */
        $newsSetup = $this->newsSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $newsSetup->installEntities();
        $entities = $newsSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $newsSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
