<?php

namespace SIT\ProductReviewNew\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Award extends AbstractDb {
	protected function _construct() {
		$this->_init('sit_productawards_flat', 'entity_id');
	}
}