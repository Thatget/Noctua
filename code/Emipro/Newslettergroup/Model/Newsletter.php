<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Model;

use Magento\Framework\Model\AbstractModel;

class Newsletter extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Emipro\Newslettergroup\Model\ResourceModel\Newsletter');
    }
}
