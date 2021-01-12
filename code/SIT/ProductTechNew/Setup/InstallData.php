<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\ProductTechNew\Setup\ProductTechnologySetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * ProductTechNew setup factory
     *
     * @var ProductTechnologySetupFactory
     */
    protected $producttechnologySetupFactory;

    /**
     * [__construct description]
     * @param ProductTechnologySetupFactory $producttechnologySetupFactory [description]
     */
    public function __construct(ProductTechnologySetupFactory $producttechnologySetupFactory)
    {
        $this->producttechnologySetupFactory = $producttechnologySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var ProductTechnologySetup $producttechnologySetup */
        $producttechnologySetup = $this->producttechnologySetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $producttechnologySetup->installEntities();
        $entities = $producttechnologySetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $producttechnologySetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
