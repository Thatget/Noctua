<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class GeneralTechnologySetup extends EavSetup {
	/**
	 * Entity type for GeneralTechnologiesNew EAV attributes
	 */
	const ENTITY_TYPE_CODE = 'sit_generaltechnologiesnew_generaltechnology';

	/**
	 * EAV Entity type for GeneralTechnologiesNew EAV attributes
	 */
	const EAV_ENTITY_TYPE_CODE = 'sit_generaltechnologiesnew';

	/**
	 * Retrieve Entity Attributes
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getAttributes() {
		$attributes = [];

		$attributes['gen_technology_title'] = [
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

		$attributes['gen_technology_image'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Image',
			'input' => 'image',
			'backend' => \SIT\GeneralTechnologiesNew\Model\GeneralTechnology\Attribute\Backend\Image::class,
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
		$attributes['gen_technology_shortdesc'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Short Description',
			'input' => 'textarea',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '1',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '30',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '1',
		];

		$attributes['gen_technology_desc'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Description',
			'input' => 'textarea',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '40',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '1',
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
			'position' => '50',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['url_key'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'URL key',
			'input' => 'text',
			'backend' => \SIT\GeneralTechnologiesNew\Model\GeneralTechnology\Attribute\Backend\UrlKey::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '60',
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
	public function getDefaultEntities() {
		$entities = [
			self::ENTITY_TYPE_CODE => [
				'entity_model' => 'SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology',
				'attribute_model' => 'SIT\GeneralTechnologiesNew\Model\ResourceModel\Eav\Attribute',
				'table' => self::ENTITY_TYPE_CODE,
				'increment_model' => null,
				'additional_attribute_table' => 'sit_generaltechnologiesnew_eav_attribute',
				'entity_attribute_collection' => 'SIT\GeneralTechnologiesNew\Model\ResourceModel\Attribute\Collection',
				'attributes' => $this->getAttributes(),
			],
		];

		return $entities;
	}
}
