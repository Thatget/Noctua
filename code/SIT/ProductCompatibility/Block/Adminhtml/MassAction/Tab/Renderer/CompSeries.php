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

class CompSeries extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
        $seriesInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SERIES);
        $seriesId = $seriesInfo->getAttributeId();
        $seriesAllOption = $this->prodCompHelper->getAttributeOptionAll($seriesId);
        foreach ($seriesAllOption as $key => $item) {
            $seriesArr[$item->getOptionId()] = $item->getValue();
        }
        if (array_key_exists($row->getCompSeries(), $seriesArr)) {
            return $seriesArr[$row->getCompSeries()];
        }
    }
}
