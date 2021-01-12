<?php

namespace SIT\ProductTechNew\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Product extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('sit_producttechnew_producttechnology_product', 'rel_id');
    }
}
