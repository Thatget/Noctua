<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\GeneralNews\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class NewsSetup extends EavSetup
{
    /**
     * Prefix for eav entity table and collection
     */
    const ENTITY_TYPE_CODE = 'sit_generalnews_news';

    /**
     * Prefix for eav attribute table and collection
     */
    const EAV_ENTITY_TYPE_CODE = 'sit_generalnews';

    /**
     * Retrieve Entity Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getAttributes()
    {
        $attributes = [];

        $attributes['news_title'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'Title',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'position' => '10',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['news_shortdesc'] = [
            'group' => 'General',
            'type' => 'text',
            'label' => 'Short Description',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '1',
        ];

        $attributes['news_desc'] = [
            'group' => 'General',
            'type' => 'text',
            'label' => 'Description',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '30',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '1',
        ];

        $attributes['status'] = [
            'group' => 'General',
            'type' => 'int',
            'label' => 'Enabled',
            'input' => 'select',
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'position' => '40',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['url_key'] = [
            'group' => 'General',
            'type' => 'varchar',
            'backend' => \SIT\GeneralNews\Model\News\Attribute\Backend\UrlKey::class,
            'frontend' => '',
            'label' => 'URL key',
            'input' => 'text',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'position' => '50',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        return $attributes;
    }

    /**
     * Retrieve default entities
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = [
            self::ENTITY_TYPE_CODE => [
                'entity_model' => 'SIT\GeneralNews\Model\ResourceModel\News',
                'attribute_model' => 'SIT\GeneralNews\Model\ResourceModel\Eav\Attribute',
                'table' => self::ENTITY_TYPE_CODE,
                'increment_model' => null,
                'additional_attribute_table' => self::EAV_ENTITY_TYPE_CODE . '_eav_attribute',
                'entity_attribute_collection' => 'SIT\GeneralNews\Model\ResourceModel\Attribute\Collection',
                'attributes' => $this->getAttributes(),
            ],
        ];

        return $entities;
    }
}
