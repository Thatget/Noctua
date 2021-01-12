<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace SIT\WebformApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Countryprice extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('sit_country_price', 'id');
    }
}
