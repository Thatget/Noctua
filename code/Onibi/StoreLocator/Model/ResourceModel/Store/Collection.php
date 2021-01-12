<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Model\ResourceModel\Store;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Onibi\StoreLocator\Model\ResourceModel\Store as StoreResourceModel;
use Onibi\StoreLocator\Model\Store as StoreModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            StoreModel::class,
            StoreResourceModel::class
        );
    }
}
