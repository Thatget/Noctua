<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Ui\Component\Listing\Column;

class NewsletterActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $uiComponentFactory;

    const URL_PATH_EDIT = 'newslettergroup/newsletter/edit';

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
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {

                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->_urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['id'],
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
