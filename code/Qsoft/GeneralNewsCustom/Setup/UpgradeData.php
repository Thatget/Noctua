<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\GeneralNewsCustom\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\GeneralNews\Setup\NewsSetup;
use SIT\GeneralNews\Setup\NewsSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class UpgradeData
 *
 * @package Qsoft\GeneralNewsCustom\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    const USE_DEFAULT_NEWS_TITLE_CONFIG = 'use_default_news_title_config';
    const USE_DEFAULT_NEWS_SHORT_DESCRIPTION_CONFIG = 'use_default_news_short_description_config';
    const USE_DEFAULT_NEWS_DESCRIPTION_CONFIG = 'use_default_news_description_config';

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * News setup factory
     *
     * @var NewsSetupFactory
     */
    protected $newsSetupFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     * @param NewsSetupFactory $newsSetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        NewsSetupFactory $newsSetupFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavAttribute = $eavAttribute;
        $this->newsSetupFactory = $newsSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
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
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            /**
             * Add attributes use_default_faq_que_config to the eav/attribute
             */
            $eavSetup->addAttribute(
                NewsSetup::ENTITY_TYPE_CODE,
                self::USE_DEFAULT_NEWS_TITLE_CONFIG,
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
                NewsSetup::ENTITY_TYPE_CODE,
                self::USE_DEFAULT_NEWS_SHORT_DESCRIPTION_CONFIG,
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

            /*$tableName = $resource->getTableName('sit_generalnews_eav_attribute');
            $newsTitleAttributeId = $this->eavAttribute->getIdByCode(
                NewsSetup::ENTITY_TYPE_CODE,
                self::USE_DEFAULT_NEWS_TITLE_CONFIG
            );
            $connection->query('INSERT INTO ' . $tableName . '(attribute_id, is_global, is_visible) VALUES'.'('.$newsTitleAttributeId.', 0, 1)');

            $newsShortDescriptionAttributeId = $this->eavAttribute->getIdByCode(
                NewsSetup::ENTITY_TYPE_CODE,
                self::USE_DEFAULT_NEWS_SHORT_DESCRIPTION_CONFIG
            );
            $connection->query('INSERT INTO ' . $tableName . '(attribute_id, is_global, is_visible) VALUES'.'('.$newsShortDescriptionAttributeId.', 0, 1)');*/
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            /**
             * Add attributes use_default_news_description_config to the eav/attribute
             */
            $eavSetup->addAttribute(
                NewsSetup::ENTITY_TYPE_CODE,
                self::USE_DEFAULT_NEWS_DESCRIPTION_CONFIG,
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
                    'position' => '90',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ]
            );
        }

        $setup->endSetup();
    }
}
