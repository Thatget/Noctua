<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Model;

use Magento\Framework\Model\AbstractModel;
use SIT\ProductFaqNew\Model\ResourceModel\Product as ProductResourceModel;

class Product extends AbstractModel {
	protected function _construct() {
		$this->_init(ProductResourceModel::class);
	}
}
