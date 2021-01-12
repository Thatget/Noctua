<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class ProsliderSliderSetup extends EavSetup {
	/**
	 * Entity type for ProSlider Main attributes
	 */
	const ENTITY_TYPE_CODE = 'mediarocks_proslider_slider_entity';

	/**
	 * Entity type for ProSlider EAV attributes
	 */
	const EAV_ENTITY_TYPE_CODE = 'mediarocks_proslider_slider';

	/**
	 * Retrieve Slider Entity Attributes
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getAttributes() {
		$attributes = [];

		$attributes['title'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slider Title',
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

		$attributes['slides'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'slides id',
			'input' => 'hidden',
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

		$attributes['page_type'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Display Page Type',
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

		$attributes['cms_page'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slider CMS Page',
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

		$attributes['sku'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slider Product Sku',
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

		$attributes['category'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slider Product Category',
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

		$attributes['custom_page'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slider Custom Page',
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

		$attributes['is_active'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Slider Status',
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

		$attributes['store_id'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Store Id',
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

		return $attributes;
	}

	/**
	 * Retrieve default entities
	 *
	 * @return array
	 */
	public function getDefaultEntities() {
		$entities = [
			self::ENTITY_TYPE_CODE => [
				'entity_model' => 'Mediarocks\ProSlider\Model\ResourceModel\Slider',
				'attribute_model' => 'Mediarocks\ProSlider\Model\ResourceModel\Eav\Slider\Attribute',
				'table' => self::ENTITY_TYPE_CODE,
				'increment_model' => null,
				'additional_attribute_table' => 'mediarocks_proslider_slider_eav_attribute',
				'entity_attribute_collection' => 'Mediarocks\ProSlider\Model\ResourceModel\Attribute\Slider\Collection',
				'attributes' => $this->getAttributes(),
			],
		];
		return $entities;
	}
}
