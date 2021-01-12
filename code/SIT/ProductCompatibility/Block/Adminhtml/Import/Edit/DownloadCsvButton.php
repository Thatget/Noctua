<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [28-05-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\Import\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DownloadCsvButton extends Generic implements ButtonProviderInterface
{
    /**
     * get download button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Download Sample CSV'),
            'class' => 'save primary',
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
        ];
    }

    /**
     * Get URL for Download button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/downloadCsv');
    }
}
