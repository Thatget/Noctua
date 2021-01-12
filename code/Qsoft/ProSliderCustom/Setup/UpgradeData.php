<?php
/**
 * Copyright © Mageworld, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProSliderCustom\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Qsoft\ProSliderCustom\Setup\InstallData;
use Mediarocks\ProSlider\Setup\ProsliderSliderSetup;

/**
 * Class UpgradeData
 *
 * @package Qsoft\ProSliderCustom\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    ) {
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * Upgrade function
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
//        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
//        $connection = $resource->getConnection();
//        if (version_compare($context->getVersion(), '1.0.1', '<')) {
//            $tableName = $resource->getTableName('mediarocks_proslider_slider_eav_attribute');
//            $sortOrderAttributeId = $this->eavAttribute->getIdByCode(ProsliderSliderSetup::ENTITY_TYPE_CODE, InstallData::SORT_ORDER);
//            $connection->query('INSERT INTO ' . $tableName . '(attribute_id, is_global, is_visible) VALUES'.'('.$sortOrderAttributeId.', 0, 1)');
//        }
        $setup->endSetup();
    }
}
