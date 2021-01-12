<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Product extends AbstractDb {
	protected function _construct() {
		$this->_init('sit_productcompatibility_productcompatibility_product', 'rel_id');
	}
}
