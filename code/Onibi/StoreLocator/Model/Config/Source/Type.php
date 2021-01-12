<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Model\Config\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options = [
            '1' => [
                'value' => [
                    ['value' => '1', 'label' => 'Retailer'],
                    ['value' => '2', 'label' => 'Reseller'],
                    ['value' => '3', 'label' => 'Distributor'],
                ],
                'label' => 'Select Type',
            ],
        ];

        return $options;
    }
}
