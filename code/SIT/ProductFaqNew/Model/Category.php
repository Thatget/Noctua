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
use SIT\ProductFaqNew\Model\ResourceModel\Category as CategoryResourceModel;

class Category extends AbstractModel {
	protected function _construct() {
		$this->_init(CategoryResourceModel::class);
	}
}
