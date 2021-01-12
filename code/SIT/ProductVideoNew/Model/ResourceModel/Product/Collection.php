<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SIT\ProductVideoNew\Model\Product as ProductModel;
use SIT\ProductVideoNew\Model\ResourceModel\Product as ProductResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(ProductModel::class, ProductResourceModel::class);
    }
}
