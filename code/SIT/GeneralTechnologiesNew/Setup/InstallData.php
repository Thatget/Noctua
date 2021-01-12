<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\GeneralTechnologiesNew\Setup\GeneralTechnologySetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * GeneralTechnologiesNew setup factory
     *
     * @var GeneralTechnologySetupFactory
     */
    protected $generaltechnologySetupFactory;

    /**
     * [__construct description]
     * @param GeneralTechnologySetupFactory $generaltechnologySetupFactory [description]
     */
    public function __construct(GeneralTechnologySetupFactory $generaltechnologySetupFactory)
    {
        $this->generaltechnologySetupFactory = $generaltechnologySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var GeneralTechnologySetup $generaltechnologySetup */
        $generaltechnologySetup = $this->generaltechnologySetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $generaltechnologySetup->installEntities();
        $entities = $generaltechnologySetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $generaltechnologySetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
