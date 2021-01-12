<?php
/**
 * Copyright © Mageworld, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageworld\ProductFaqNewCustom\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mageworld\ProductFaqNewCustom\Setup\InstallData;
use SIT\ProductFaqNew\Setup\ProductFaqSetup;

/**
 * Class UpgradeData
 *
 * @package Mageworld\ProductFaqNewCustom\Setup
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $tableName = $resource->getTableName('sit_productfaqnew_eav_attribute');
            $faqQueAttributeId = $this->eavAttribute->getIdByCode(ProductFaqSetup::ENTITY_TYPE_CODE, InstallData::USE_DEFAULT_FAQ_QUE_CONFIG);
            $faqAnsAttributeId = $this->eavAttribute->getIdByCode(ProductFaqSetup::ENTITY_TYPE_CODE, InstallData::USE_DEFAULT_FAQ_ANS_CONFIG);

            $connection->query('INSERT INTO ' . $tableName . '(attribute_id, is_global, is_visible) VALUES'.'('.$faqQueAttributeId.', 0, 1)');
            $connection->query('INSERT INTO ' . $tableName . '(attribute_id, is_global, is_visible) VALUES'.'('.$faqAnsAttributeId.', 0, 1)');
        }
    }
}
