<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Mainpagevideo\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\Mainpagevideo\Setup\MainpagevideoSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * Mainpagevideo setup factory
     *
     * @var MainpagevideoSetupFactory
     */
    protected $mainpagevideoSetupFactory;

    /**
     * Init
     *
     * @param MainpagevideoSetupFactory $mainpagevideoSetupFactory
     */
    public function __construct(MainpagevideoSetupFactory $mainpagevideoSetupFactory)
    {
        $this->mainpagevideoSetupFactory = $mainpagevideoSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var MainpagevideoSetup $mainpagevideoSetup */
        $mainpagevideoSetup = $this->mainpagevideoSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $mainpagevideoSetup->installEntities();
        $entities = $mainpagevideoSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $mainpagevideoSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
