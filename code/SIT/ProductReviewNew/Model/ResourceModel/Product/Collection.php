<?php

namespace SIT\ProductReviewNew\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SIT\ProductReviewNew\Model\Product as ProductModel;
use SIT\ProductReviewNew\Model\ResourceModel\Product as ProductResourceModel;

class Collection extends AbstractCollection {
	protected function _construct() {
		$this->_init(ProductModel::class, ProductResourceModel::class);
	}
}
