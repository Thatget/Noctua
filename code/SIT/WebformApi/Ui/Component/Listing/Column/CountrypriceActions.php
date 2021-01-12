<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace SIT\WebformApi\Ui\Component\Listing\Column;

class CountrypriceActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    const URL_DELETE_PATH = 'webformapi/index/delete';

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
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_DELETE_PATH,
                                [
                                    'id' => $item['id'],
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Record'),
                                'message' => __('Are you sure you wan\'t to delete a record?'),
                            ],
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}
