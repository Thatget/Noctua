<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace SIT\WebformApi\Model\ResourceModel\Countryprice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SIT\WebformApi\Model\Countryprice as CountrypriceModel;
use SIT\WebformApi\Model\ResourceModel\Countryprice as CountrypriceResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            CountrypriceModel::class,
            CountrypriceResourceModel::class
        );
    }
}
