<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\LanguageTranslator\Model;

use Magento\Framework\Model\AbstractModel;
use SIT\LanguageTranslator\Model\ResourceModel\Tranlation as TranlationResourceModel;

class Tranlation extends AbstractModel {
	protected function _construct() {
		$this->_init(TranlationResourceModel::class);
	}
}
