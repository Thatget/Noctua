<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Ui\Component\Listing\Column;

class StoreActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    const URL_EDIT_PATH = 'onibi_storelocator/index/edit';

    /**
     * [__construct description]
     * @param \Magento\Framework\UrlInterface                              $urlBuilder         [description]
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            [description]
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory [description]
     * @param array                                                        $components         [description]
     * @param array                                                        $data               [description]
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_EDIT_PATH,
                                [
                                    'entity_id' => $item['entity_id'],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}
