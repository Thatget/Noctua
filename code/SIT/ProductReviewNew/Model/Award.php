<?php

namespace SIT\ProductReviewNew\Model;

use Magento\Framework\Model\AbstractModel;
use SIT\ProductReviewNew\Model\ResourceModel\Award as AwardResourceModel;

class Award extends AbstractModel {
	protected function _construct() {
		$this->_init(AwardResourceModel::class);
	}
}