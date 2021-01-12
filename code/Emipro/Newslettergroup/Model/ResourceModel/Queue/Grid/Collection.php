<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Model\ResourceModel\Queue\Grid;

class Collection extends \Emipro\Newslettergroup\Model\ResourceModel\Queue\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscribersInfo();
        return $this;
    }
}
