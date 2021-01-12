<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [1-04-2019]
 */
namespace SIT\ProductTechNew\Ui\Component\Listing\Column;

class ShortDesc extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Template
     */
    protected $template;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            [description]
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory [description]
     * @param \Magento\Framework\View\Element\Template                     $template           [description]
     * @param array                                                        $components         [description]
     * @param array                                                        $data               [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\View\Element\Template $template,
        array $components = [],
        array $data = []
    ) {
        $this->template = $template;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (array_key_exists('technology_shortdesc', $item)) {
                    $imageUrl = $this->template->getViewFileUrl("SIT_ProductTechNew::images/short_desc.gif");
                    $item['technology_shortdesc'] = $item['technology_shortdesc'];
                    $badgeArray = [];
                    $imagesContainer = '';
                    $imagesArray = [
                        [
                            'image_url' => $item['technology_shortdesc'],
                        ],
                    ];
                    foreach ($imagesArray as $image) {
                        $imagesContainer = $imagesContainer . "<img alt='" . strip_tags($item['technology_shortdesc']) . "' src=" . $imageUrl . "  title='" . strip_tags($item['technology_shortdesc']) . "' style='display:inline-block;margin:2px;width:unset;border:none;'/>";
                    }
                    $item['technology_shortdesc'] = $imagesContainer;
                }
            }
        }
        return $dataSource;
    }
}
