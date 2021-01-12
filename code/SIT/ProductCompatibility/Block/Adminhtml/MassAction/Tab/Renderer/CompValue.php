<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [06-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer;

use Magento\Framework\DataObject;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

class CompValue extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * [__construct description]
     * @param ProductCompHelper $prodCompHelper [description]
     */
    public function __construct(
        ProductCompHelper $prodCompHelper
    ) {
        $this->prodCompHelper = $prodCompHelper;
    }

    /**
     * [render description]
     * @param  DataObject $row [description]
     * @return [type]          [description]
     */
    public function render(DataObject $row)
    {
        $compValueInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_VALUE);
        $compValueId = $compValueInfo->getAttributeId();
        $compValueAllOption = $this->prodCompHelper->getAttributeOptionAll($compValueId);
        foreach ($compValueAllOption as $key => $item) {
            $compValueArr[$item->getOptionId()] = $item->getValue();
        }
        if (array_key_exists($row->getCompValue(), $compValueArr)) {
            return $compValueArr[$row->getCompValue()];
        }
    }
}
