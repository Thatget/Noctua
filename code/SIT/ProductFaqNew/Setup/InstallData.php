<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\ProductFaqNew\Setup\ProductFaqSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * ProductFaqNew setup factory
     *
     * @var ProductFaqSetupFactory
     */
    protected $productfaqSetupFactory;

    /**
     * [__construct description]
     * @param ProductFaqSetupFactory $productfaqSetupFactory [description]
     */
    public function __construct(ProductFaqSetupFactory $productfaqSetupFactory)
    {
        $this->productfaqSetupFactory = $productfaqSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var ProductFaqNewSetup $productfaqnewSetup */
        $productfaqnewSetup = $this->productfaqSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $productfaqnewSetup->installEntities();
        $entities = $productfaqnewSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $productfaqnewSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
