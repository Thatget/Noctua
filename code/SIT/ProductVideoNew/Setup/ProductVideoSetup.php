<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class ProductVideoSetup extends EavSetup
{
    /**
     * Entity type for Mainapgevideo EAV attributes
     */
    const ENTITY_TYPE_CODE = 'sit_productvideonew_productvideo';

    /**
     * EAV Entity type for Mainapgevideo EAV attributes
     */
    const EAV_ENTITY_TYPE_CODE = 'sit_productvideonew';

    /**
     * Retrieve Entity Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getAttributes()
    {
        $attributes = [];

        $attributes['video_title'] = [
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

        $attributes['video_link'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'URL',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['video_link_cn'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'China Alternative Url',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '25',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];
        $attributes['video_reviewer_url'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'URL',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '30',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];
        $attributes['video_reviewer_url_cn'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'China Alternative Reviewer Url',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '35',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];
        $attributes['video_thumbnail_cn'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'China Alternative Video Thumbnail Url',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '37',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];
        $attributes['video_language'] = [
            'group' => 'General',
            'type' => 'int',
            'label' => 'Language',
            'input' => 'select',
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '40',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
            'option' => [
                'values' =>
                [
                    'Arabic',
                    'Bosnian',
                    'Bulgarian',
                    'Chinese',
                    'Croatian',
                    'Czech',
                    'Danish',
                    'Dutch',
                    'English',
                    'Estonian',
                    'Finnish',
                    'French',
                    'German',
                    'Greek',
                    'Hebrew',
                    'Hungarian',
                    'Indonesian',
                    'Italian',
                    'Japanese',
                    'Korean',
                    'Latvian',
                    'Lithuanian',
                    'Montenegrin',
                    'Norwegian',
                    'Persian',
                    'Polish',
                    'Portuguese',
                    'Romanian',
                    'Russian',
                    'Serbian',
                    'Slovak',
                    'Slovenian',
                    'Spanish',
                    'Swedish',
                    'Thai',
                    'Turkish',
                    'Ukrainian',
                    'Vietnamese',
                ],
            ],
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
                'entity_model' => 'SIT\ProductVideoNew\Model\ResourceModel\ProductVideo',
                'attribute_model' => 'SIT\ProductVideoNew\Model\ResourceModel\Eav\Attribute',
                'table' => self::ENTITY_TYPE_CODE,
                'increment_model' => null,
                'additional_attribute_table' => 'sit_productvideonew_eav_attribute',
                'entity_attribute_collection' => 'SIT\ProductVideoNew\Model\ResourceModel\Attribute\Collection',
                'attributes' => $this->getAttributes(),
            ],
        ];

        return $entities;
    }
}
