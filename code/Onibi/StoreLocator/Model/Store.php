<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Model;

use Magento\Framework\Model\AbstractModel;
use Onibi\StoreLocator\Model\ResourceModel\Store as StoreResourceModel;

class Store extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(StoreResourceModel::class);
    }
}
