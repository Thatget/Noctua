<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [19-04-2019]
 */

namespace Emipro\Newslettergroup\Block\Adminhtml\Subscriber\Grid\Column\Renderer;

use Magento\Framework\DataObject;

class Sourcewebsite extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var Magento\Catalog\Model\ResourceModel\ProductFactory
     */
    protected $_attributeLoading;

    /**
     * [__construct description]
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory $attributeLoading [description]
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\ProductFactory $attributeLoading
    ) {
        $this->_attributeLoading = $attributeLoading;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        /**
         * Get source_website label
         */
        $poductReource = $this->_attributeLoading->create();
        $attr = $poductReource->getAttribute('newsletter_website');
        $optionText = $attr->getSource()->getOptionText($row->getData('source_website'));
        return $optionText;
    }
}
