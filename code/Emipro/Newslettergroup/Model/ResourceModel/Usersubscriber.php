<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Usersubscriber extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('emipro_newsletter_user_subscriber', 'id');
    }
}
