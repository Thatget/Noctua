<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class ProductCompatibilitySetup extends EavSetup {
	/**
	 * Entity type for Compatibility EAV attributes
	 */
	const ENTITY_TYPE_CODE = 'sit_productcompatibility_productcompatibility';

	/**
	 * EAV Entity type for Compatibility EAV attributes
	 */
	const EAV_ENTITY_TYPE_CODE = 'sit_productcompatibility';

	/**
	 * Retrieve Entity Attributes
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getAttributes() {
		$attributes = [];

		$attributes['comp_comment'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Comment',
			'input' => 'textarea',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '10',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['comp_manufacture'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Manufacture',
			'input' => 'select',
			'source' => \SIT\ProductCompatibility\Model\ResourceModel\Attribute\Source\CompManufacturer::class,
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

		$attributes['comp_model'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Model',
			'input' => 'select',
			'source' => \SIT\ProductCompatibility\Model\ResourceModel\Attribute\Source\CompModel::class,
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

		$attributes['comp_series'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Series',
			'input' => 'select',
			'source' => \SIT\ProductCompatibility\Model\ResourceModel\Attribute\Source\CompSeries::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '1',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '40',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['comp_socket'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Socket',
			'input' => 'select',
			'source' => \SIT\ProductCompatibility\Model\ResourceModel\Attribute\Source\CompSocket::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => false,
			'default' => '',
			'unique' => false,
			'position' => '50',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['comp_title'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Title',
			'input' => 'text',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => false,
			'default' => '',
			'unique' => false,
			'position' => '60',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['comp_type'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Type',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '1',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '70',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['comp_value'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Compatbility',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '1',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '80',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['template_text_1'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Select Template Text 1',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '90',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['template_text_2'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Select Template Text 2',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '100',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['template_text_3'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Select Template Text 3',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '110',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['template_text_4'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Select Template Text 4',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '120',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['status'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Status',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => false,
			'default' => '',
			'unique' => false,
			'position' => '130',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['comp_extra_comment'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Extra Comment',
			'input' => 'text',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => false,
			'default' => '',
			'unique' => false,
			'position' => '140',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['url_key'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'URL Key',
			'input' => 'text',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => false,
			'default' => '',
			'unique' => false,
			'position' => '150',
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
				'entity_model' => 'SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility',
				'attribute_model' => 'SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute',
				'table' => self::ENTITY_TYPE_CODE,
				'increment_model' => null,
				'additional_attribute_table' => 'sit_productcompatibility_eav_attribute',
				'entity_attribute_collection' => 'SIT\ProductCompatibility\Model\ResourceModel\Attribute\Collection',
				'attributes' => $this->getAttributes(),
			],
		];

		return $entities;
	}
}
