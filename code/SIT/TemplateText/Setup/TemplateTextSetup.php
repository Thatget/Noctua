<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\TemplateText\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class TemplateTextSetup extends EavSetup
{
    /**
     * Entity type for TemplateText EAV attributes
     */
    const ENTITY_TYPE_CODE = 'sit_templatetext_templatetext';

    /**
     * EAV Entity type for TemplateText EAV attributes
     */
    const EAV_ENTITY_TYPE_CODE = 'sit_templatetext';

    /**
     * Retrieve Entity Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getAttributes()
    {
        $attributes = [];

        $attributes['template_text_title'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'Template Text Title',
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

        $attributes['template_text_comment'] = [
            'group' => 'General',
            'type' => 'text',
            'label' => 'Template Text Comment',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '20',
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
            'position' => '30',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes['url_key'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'URL key',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => true,
            'default' => '',
            'unique' => false,
            'position' => '40',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
            'readonly' => true,
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
                'entity_model' => 'SIT\TemplateText\Model\ResourceModel\TemplateText',
                'attribute_model' => 'SIT\TemplateText\Model\ResourceModel\Eav\Attribute',
                'table' => self::ENTITY_TYPE_CODE,
                'increment_model' => null,
                'additional_attribute_table' => 'sit_templatetext_eav_attribute',
                'entity_attribute_collection' => 'SIT\TemplateText\Model\ResourceModel\Attribute\Collection',
                'attributes' => $this->getAttributes(),
            ],
        ];

        return $entities;
    }
}
