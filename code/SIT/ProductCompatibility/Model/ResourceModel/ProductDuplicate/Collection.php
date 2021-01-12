<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Model\ResourceModel\ProductDuplicate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SIT\ProductCompatibility\Model\ProductDuplicate as ProductDuplicateModel;
use SIT\ProductCompatibility\Model\ResourceModel\ProductDuplicate as ProductDuplicateResourceModel;

class Collection extends AbstractCollection {
	protected function _construct() {
		$this->_init(ProductDuplicateModel::class, ProductDuplicateResourceModel::class);
	}
}
