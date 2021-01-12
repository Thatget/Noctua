<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [25-3-2019]
 */

namespace Mediarocks\ProSlider\Ui\Component\Listing\Slider\DataProvider;

class Document extends \Magento\Framework\View\Element\UiComponent\DataProvider\Document {
	protected $_idFieldName = 'entity_id';

	public function getIdFieldName() {
		return $this->_idFieldName;
	}
}
