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

class ProsliderSlideSetup extends EavSetup {

	/**
	 * Entity type for ProSlider Main attributes
	 */
	const ENTITY_TYPE_CODE_SL = 'mediarocks_proslider_slide_entity';

	/**
	 * Entity type for ProSlider EAV attributes
	 */
	const EAV_ENTITY_TYPE_CODE_SL = 'mediarocks_proslider_slide';

	/**
	 * Retrieve Slider Entity Attributes
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getAttributes() {
		$attributes = [];

		$attributes['slide_name'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slide Name',
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

		$attributes['image'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slide Image',
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
			'label' => 'Slide Status',
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

		$attributes['title'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Slide Title',
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

		$attributes['is_show_title_in_slide'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Title Status',
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
		$attributes['image_link'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Slide Image Link',
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
			self::ENTITY_TYPE_CODE_SL => [
				'entity_model' => 'Mediarocks\ProSlider\Model\ResourceModel\Slide',
				'attribute_model' => 'Mediarocks\ProSlider\Model\ResourceModel\Eav\Slide\Attribute',
				'table' => self::ENTITY_TYPE_CODE_SL,
				'increment_model' => null,
				'additional_attribute_table' => 'mediarocks_proslider_slide_eav_attribute',
				'entity_attribute_collection' => 'Mediarocks\ProSlider\Model\ResourceModel\Attribute\Slide\Collection',
				'attributes' => $this->getAttributes(),
			],
		];
		return $entities;
	}
}
