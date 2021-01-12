<?php

namespace SIT\ProductReviewNew\Model\ResourceModel\Award;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SIT\ProductReviewNew\Model\Award as AwardModel;
use SIT\ProductReviewNew\Model\ResourceModel\Award as AwardResourceModel;

class Collection extends AbstractCollection {
	protected function _construct() {
		$this->_init(AwardModel::class, AwardResourceModel::class);
	}
}
