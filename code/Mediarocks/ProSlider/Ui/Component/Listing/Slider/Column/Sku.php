<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [25-3-2019]
 */
namespace Mediarocks\ProSlider\Ui\Component\Listing\Slider\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Mediarocks\ProSlider\Model\SliderFactory;

class Sku extends Column
{

    /**
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * [__construct description]
     * @param ContextInterface   $context            [description]
     * @param UiComponentFactory $uiComponentFactory [description]
     * @param array              $components         [description]
     * @param array              $data               [description]
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        SliderFactory $sliderFactory,
        array $data = []
    ) {
        $this->sliderFactory = $sliderFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $item['sku'] = $this->sliderFactory->create()->load($item['entity_id'])->getSku();
                    $item['sku'] = json_decode(unserialize($item['sku']));
                }
            }
        }
        return $dataSource;
    }
}
