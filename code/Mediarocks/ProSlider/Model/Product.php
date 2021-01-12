<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Model;

use Magento\Framework\Model\AbstractModel;
use Mediarocks\ProSlider\Model\ResourceModel\Product as ProductResourceModel;

class Product extends AbstractModel {
	protected function _construct() {
		$this->_init(ProductResourceModel::class);
	}
}
