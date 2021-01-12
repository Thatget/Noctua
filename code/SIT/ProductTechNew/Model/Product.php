<?php

namespace SIT\ProductTechNew\Model;

use Magento\Framework\Model\AbstractModel;
use SIT\ProductTechNew\Model\ResourceModel\Product as ProductResourceModel;

class Product extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ProductResourceModel::class);
    }
}
