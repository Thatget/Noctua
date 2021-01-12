<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class ProductReviewSetup extends EavSetup {
	/**
	 * Entity type for Mainapgevideo EAV attributes
	 */
	const ENTITY_TYPE_CODE = 'sit_productreviewnew_productreview';

	/**
	 * EAV Entity type for Mainapgevideo EAV attributes
	 */
	const EAV_ENTITY_TYPE_CODE = 'sit_productreviewnew';

	/**
	 * Retrieve Entity Attributes
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function getAttributes() {
		$attributes = [];

		$attributes['product_review_priority'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Product Awards Priority',
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
		];

		$attributes['review_country'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Country',
			'input' => 'select',
			'source' => \SIT\ProductReviewNew\Model\ProductReview\Attribute\Source\Country::class,
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

		$attributes['review_desc'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Description',
			'input' => 'textarea',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '50',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['review_image'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Image',
			'input' => 'Image',
			'backend' => \SIT\ProductReviewNew\Model\ProductReview\Attribute\Backend\Image::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '60',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['review_link'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Link',
			'input' => 'text',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '70',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['review_position'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Awards Priority',
			'input' => 'text',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '20',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['review_short_desc'] = [
			'group' => 'General',
			'type' => 'text',
			'label' => 'Short Description',
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

		$attributes['review_site_link'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Website Link',
			'input' => 'text',
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '69',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['review_startpage'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Review Start Page',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '101',
			'note' => '',
			'visible' => '1',
			'wysiwyg_enabled' => '0',
		];

		$attributes['review_title'] = [
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

		$attributes['review_website'] = [
			'group' => 'General',
			'type' => 'varchar',
			'label' => 'Website',
			'input' => 'text',
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

		$attributes['r_lng'] = [
			'group' => 'General',
			'type' => 'int',
			'label' => 'Review Language',
			'input' => 'select',
			'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
			'global' => ScopedAttributeInterface::SCOPE_STORE,
			'required' => '0',
			'user_defined' => true,
			'default' => '',
			'unique' => false,
			'position' => '80',
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
				],
			],
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
			'position' => '100',
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
				'entity_model' => 'SIT\ProductReviewNew\Model\ResourceModel\ProductReview',
				'attribute_model' => 'SIT\ProductReviewNew\Model\ResourceModel\Eav\Attribute',
				'table' => self::ENTITY_TYPE_CODE,
				'increment_model' => null,
				'additional_attribute_table' => self::EAV_ENTITY_TYPE_CODE . '_eav_attribute',
				'entity_attribute_collection' => 'SIT\ProductReviewNew\Model\ResourceModel\Attribute\Collection',
				'attributes' => $this->getAttributes(),
			],
		];

		return $entities;
	}
}
