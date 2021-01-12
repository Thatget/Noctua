<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [27-04-2019]
 */

namespace VladimirPopov\WebFormsDepend\Model;

class Field extends \VladimirPopov\WebForms\Model\Field {
	public function getFieldTypes() {
		$types = new \Magento\Framework\DataObject(array(
			"text" => __('Text'),
			"email" => __('Text / E-mail'),
			"number" => __('Text / Number'),
			"url" => __('Text / URL'),
			"password" => __('Text / Password'),
			"autocomplete" => __('Text / Auto-complete'),
			"textarea" => __('Textarea'),
			"wysiwyg" => __('HTML Editor'),
			"select" => __('Select'),
			"select/radio" => __('Select / Radio'),
			"select/checkbox" => __('Select / Checkbox'),
			"select/contact" => __('Select / Contact'),
			"country" => __('Select / Country'),
			"subscribe" => __('Newsletter Subscription / Checkbox'),
			"date" => __('Date'),
			"datetime" => __('Date / Time'),
			"date/dob" => __('Date of Birth'),
			"stars" => __('Star Rating'),
			"file" => __('File Upload'),
			"image" => __('Image Upload'),
			"html" => __('HTML Block'),
			/*Changed by MD for Custom express delivery field[START][27-04-2019]*/
			"delivery" => __('Express Delivery / Radio'),
			"vat" => __('Text / VAT'),
			/*Changed by MD for Custom express delivery field[END][27-04-2019]*/
		));

		$types->setData('hidden', __('Hidden'));

		// add more field types
		$this->_eventManager->dispatch('webforms_fields_types', array('types' => $types));

		return $types->getData();
	}

}
