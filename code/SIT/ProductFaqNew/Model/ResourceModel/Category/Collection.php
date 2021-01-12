<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SIT\ProductFaqNew\Model\Category as CategoryModel;
use SIT\ProductFaqNew\Model\ResourceModel\Category as CategoryResourceModel;

class Collection extends AbstractCollection {
	protected $_idFieldName = 'id';
	protected function _construct() {
		$this->_init(CategoryModel::class, CategoryResourceModel::class);
	}
}
