<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [19-06-2019]
 */
namespace SIT\ProductTechNew\Block\Adminhtml\Tab\Renderer;

use Magento\Framework\DataObject;

class Description extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Template
     */
    protected $template;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template $template [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template $template
    ) {
        $this->template = $template;
    }
    /**
     * [render description]
     * @param  DataObject $row [description]
     * @return [type]          [description]
     */
    public function render(DataObject $row)
    {
        $description = strip_tags($row->getData('technology_shortdesc'));

        if (!$description) {
            return '';
        }

        $iconUrl = $this->template->getViewFileUrl("SIT_ProductTechNew::images/short_desc.gif");
        $descriptionPreview = sprintf(
            '<img alt="preview" src="%s" title="%s" style="margin: 0px auto;display: block;"/>',
            $iconUrl,
            $description
        );
        return $descriptionPreview;
    }
}
