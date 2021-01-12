<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class ProductFaqSetup extends EavSetup {
	/**
	 * Entity type for Mainapgevideo EAV attributes
	 */
	const ENTITY_TYPE_CODE = 'sit_productfaqnew_productfaq';

	/**
	 * EAV Entity type for Mainapgevideo EAV attributes
	 */
	const EAV_ENTITY_TYPE_CODE = 'sit_productfaqnew';

	/**
	 * Retrieve Entity Attributes
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getAttributes() {
		$attributes = [];

		$attributes['faq_que'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'FAQ Question',
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

		$attributes['faq_ans'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'FAQ Answer',
			'input' => 'textarea',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '1',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '20',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '',
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
			'position' => '30',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['faq_category'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Category',
			'input' => 'select',
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
		$attributes['faq_multi_category'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Category',
			'input' => 'multiselect',
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

		$attributes['url_key'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'URL key',
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
				'entity_model' => 'SIT\ProductFaqNew\Model\ResourceModel\ProductFaq',
				'attribute_model' => 'SIT\ProductFaqNew\Model\ResourceModel\Eav\Attribute',
				'table' => self::ENTITY_TYPE_CODE,
				'increment_model' => null,
				'additional_attribute_table' => 'sit_productfaqnew_eav_attribute',
				'entity_attribute_collection' => 'SIT\ProductFaqNew\Model\ResourceModel\Attribute\Collection',
				'attributes' => $this->getAttributes(),
			],
		];

		return $entities;
	}
}
