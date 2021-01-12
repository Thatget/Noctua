<?php

namespace SIT\ProductReviewNew\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Product extends AbstractDb {
	protected function _construct() {
		$this->_init('sit_productreviewnew_productreview_product', 'rel_id');
	}
}
