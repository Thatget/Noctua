<?php

namespace SIT\ProductReviewNew\Model;

use Magento\Framework\Model\AbstractModel;
use SIT\ProductReviewNew\Model\ResourceModel\Product as ProductResourceModel;

class Product extends AbstractModel {
	protected function _construct() {
		$this->_init(ProductResourceModel::class);
	}
}
