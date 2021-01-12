<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-04-2019]
 */

namespace BL\FileAttributes\Model\Product\Attribute\Frontend;

class File extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
{
    public function getValue(\Magento\Framework\DataObject $object)
    {
        $this->getAttribute()->setData(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::IS_HTML_ALLOWED_ON_FRONT, 1);
        $data = '';
        $value = parent::getValue($object);

        return "<b>" . $value . "</b>";
    }
}
