<?php
/**
 * Copyright © Mageworld, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProSliderCustom\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mediarocks\ProSlider\Setup\ProsliderSliderSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 *
 * @package Qsoft\ProSliderCustom\Setup
 */
class InstallData implements InstallDataInterface
{
    const SORT_ORDER = 'sort_order';

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * InstallData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        /**
         * Add attributes use_default_faq_que_config to the eav/attribute
         */
        $eavSetup->addAttribute(
            ProsliderSliderSetup::ENTITY_TYPE_CODE,
            self::SORT_ORDER,
            [

                'group' => 'General',
                'type' => 'int',
                'label' => 'Sort Order',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'required' => '',
                'user_defined' => false,
                'default' => 0,
                'unique' => false,
                'position' => '70',
                'note' => '',
                'visible' => '1',
                'wysiwyg_enabled' => '0',
            ]
        );

        $setup->endSetup();
    }
}
