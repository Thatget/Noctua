<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Mainpagevideo\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class MainpagevideoSetup extends EavSetup
{
    /**
     * Entity type for Mainapgevideo EAV attributes
     */
    const ENTITY_TYPE_CODE = 'sit_mainpagevideo_mainpagevideo';

    /**
     * EAV Entity type for Mainapgevideo EAV attributes
     */
    const EAV_ENTITY_TYPE_CODE = 'sit_mainpagevideo';

    /**
     * Retrieve Entity Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getAttributes()
    {
        $attributes = [];

        $attributes['main_video_title'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'Video / Merchandise Title',
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

        $attributes['main_video_url'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'Video / Merchandise Url',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
            'readonly' => true,
        ];

        $attributes['main_video_url_cn'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'China Alternative Video / Merchandise Url',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '25',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
            'readonly' => true,
        ];

        $attributes['merchendise_image'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'Merchandise Image',
            'input' => 'image',
            'backend' => \SIT\Mainpagevideo\Model\Mainpagevideo\Attribute\Backend\Image::class,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '30',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['main_video_desc'] = [
            'group' => 'General',
            'type' => 'text',
            'label' => 'Video Description',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '40',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['new_label'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'Label',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '50',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
            'readonly' => true,
        ];

        $attributes['label_status'] = [
            'group' => 'General',
            'type' => 'int',
            'label' => 'Label Enabled',
            'input' => 'select',
            'source' => \Magento\Catalog\Model\Product\Attribute\Source\Status::class,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '',
            'user_defined' => false,
            'default' => '1',
            'unique' => false,
            'position' => '60',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['position'] = [
            'group' => 'General',
            'type' => 'int',
            'label' => 'Position',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '',
            'user_defined' => false,
            'default' => 0,
            'unique' => false,
            'position' => '70',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['status'] = [
            'group' => 'General',
            'type' => 'int',
            'label' => 'Enabled',
            'input' => 'select',
            'source' => \Magento\Catalog\Model\Product\Attribute\Source\Status::class,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '',
            'user_defined' => false,
            'default' => '1',
            'unique' => false,
            'position' => '80',
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
                'entity_model' => 'SIT\Mainpagevideo\Model\ResourceModel\Mainpagevideo',
                'attribute_model' => 'SIT\Mainpagevideo\Model\ResourceModel\Eav\Attribute',
                'table' => self::ENTITY_TYPE_CODE,
                'increment_model' => null,
                'additional_attribute_table' => 'sit_mainpagevideo_eav_attribute',
                'entity_attribute_collection' => 'SIT\Mainpagevideo\Model\ResourceModel\Attribute\Collection',
                'attributes' => $this->getAttributes(),
            ],
        ];

        return $entities;
    }
}
