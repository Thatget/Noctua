<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\ProductVideoNew\Setup\ProductVideoSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * ProductFaqNew setup factory
     *
     * @var ProductVideoSetupFactory
     */
    protected $productvideonewSetupFactory;

    /**
     * [__construct description]
     * @param ProductVideoSetupFactory $productvideonewSetupFactory [description]
     */
    public function __construct(ProductVideoSetupFactory $productvideonewSetupFactory)
    {
        $this->productvideonewSetupFactory = $productvideonewSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var ProductFaqNewSetup $productvideoSetup */
        $productvideoSetup = $this->productvideonewSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $productvideoSetup->installEntities();
        $entities = $productvideoSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $productvideoSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
