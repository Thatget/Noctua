<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use SIT\ProductFaqNew\Setup\ProductFaqSetup;

class UpgradeData implements UpgradeDataInterface
{

    const OLD_EAV_ENTITY_TYPE = 'sip_productfaqnew_productfaq';
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * [__construct description]
     * @param EavSetupFactory $eavSetupFactory [description]
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $entityType = $eavSetup->getEntityTypeId(self::OLD_EAV_ENTITY_TYPE);
            if ($entityType) {
                $new_entity_type = $eavSetup->getEntityTypeId(ProductFaqSetup::ENTITY_TYPE_CODE);
                $data = [
                    'entity_type_code' => ProductFaqSetup::ENTITY_TYPE_CODE,
                    'entity_model' => \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq::class,
                    'attribute_model' => \SIT\ProductFaqNew\Model\ResourceModel\Eav\Attribute::class,
                    'entity_table' => ProductFaqSetup::ENTITY_TYPE_CODE,
                    'entity_attribute_collection' => \SIT\ProductFaqNew\Model\ResourceModel\Attribute\Collection::class,
                ];
                $eavSetup->updateEntityType(self::OLD_EAV_ENTITY_TYPE, $data);
                $eavSetup->removeEntityType($new_entity_type);
            }
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            /** @var EavSetupFactory $eavSetupFactory */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            /**
             * Add attributes to the eav/attribute
             */

            $eavSetup->addAttribute(
                ProductFaqSetup::ENTITY_TYPE_CODE,
                'category_id',
                [

                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Category ID',
                    'input' => 'select',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '',
                    'unique' => false,
                    'position' => '70',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ]
            );
        }
        $setup->endSetup();
    }
}
