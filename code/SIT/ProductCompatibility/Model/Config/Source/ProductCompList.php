<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [28-05-2019]
 */
namespace SIT\ProductCompatibility\Model\Config\Source;

class ProductCompList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => 'Mainboard', 'label' => __('Mainboard')],
            ['value' => 'RAM', 'label' => __('RAM')],
            ['value' => 'CPU', 'label' => __('CPU')],
            ['value' => 'Case', 'label' => __('Case')],
        ];
    }
}
