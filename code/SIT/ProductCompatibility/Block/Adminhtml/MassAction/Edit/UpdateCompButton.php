<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [28-05-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\MassAction\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class UpdateCompButton implements ButtonProviderInterface
{
    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Update Compatibilities'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
        return $data;
    }
}
