<?php
/**
 * Copyright © Mageworld, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageworld\ProductFaqNewCustom\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\ProductFaqNew\Setup\ProductFaqSetup;
use SIT\ProductFaqNew\Setup\ProductFaqSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 *
 * @package Mageworld\ProductFaqNewCustom\Setup
 */
class InstallData implements InstallDataInterface
{
    const USE_DEFAULT_FAQ_QUE_CONFIG = 'use_default_faq_que_config';
    const USE_DEFAULT_FAQ_ANS_CONFIG = 'use_default_faq_ans_config';

    /**
     * ProductFaqNew setup factory
     *
     * @var ProductFaqSetupFactory
     */
    protected $productfaqSetupFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * [__construct description]
     * @param ProductFaqSetupFactory $productfaqSetupFactory [description]
     */
    public function __construct(
        ProductFaqSetupFactory $productfaqSetupFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->productfaqSetupFactory = $productfaqSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
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
            ProductFaqSetup::ENTITY_TYPE_CODE,
            self::USE_DEFAULT_FAQ_QUE_CONFIG,
            [

                'group' => 'General',
                'type' => 'int',
                'label' => 'Use default config',
                'input' => 'select',
                'source' => \Magento\Catalog\Model\Product\Attribute\Source\Status::class,
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

        /**
         * Add attributes use_default_faq_ans_config to the eav/attribute
         */
        $eavSetup->addAttribute(
            ProductFaqSetup::ENTITY_TYPE_CODE,
            self::USE_DEFAULT_FAQ_ANS_CONFIG,
            [

                'group' => 'General',
                'type' => 'int',
                'label' => 'Use default config',
                'input' => 'select',
                'source' => \Magento\Catalog\Model\Product\Attribute\Source\Status::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'required' => '',
                'user_defined' => false,
                'default' => '',
                'unique' => false,
                'position' => '80',
                'note' => '',
                'visible' => '1',
                'wysiwyg_enabled' => '0',
            ]
        );

        $setup->endSetup();
    }
}
