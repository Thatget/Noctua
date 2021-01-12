<?php
/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Code standard by : MD [10-07-2019]
 */

namespace SIT\CustomVariables\Block\Grid\Renderer;

/**
 * Renderer class for action in the admin notifications grid
 *
 * @package Magento\AdminNotification\Block\Grid\Renderer
 */
class Actions extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Context      $context    [description]
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder [description]
     * @param array                               $data       [description]
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $href = $this->getUrl(
            'sit_customvariables/customvariable/duplicate/',
            [
                '_current' => true,
                'variable_id' => $row->getVariableId(),
            ]);

        $url = $this->urlBuilder->getUrl($href);
        return '<a class="sit-admin-anchor-button" href="' . $url . '" >' . __('Duplicate') . '</a>';
    }
}
