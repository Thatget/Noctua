<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\LanguageTranslator\Model\ResourceModel\Tranlation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SIT\LanguageTranslator\Model\ResourceModel\Tranlation as TranlationResourceModel;
use SIT\LanguageTranslator\Model\Tranlation as TranlationModel;

class Collection extends AbstractCollection {
	protected function _construct() {
		$this->_init(
			TranlationModel::class,
			TranlationResourceModel::class
		);
	}
}
