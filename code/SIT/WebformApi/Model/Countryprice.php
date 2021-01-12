<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace SIT\WebformApi\Model;

use Magento\Framework\Model\AbstractModel;
use SIT\WebformApi\Model\ResourceModel\Countryprice as CountrypriceResourceModel;

class Countryprice extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(CountrypriceResourceModel::class);
    }
}
