<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductTechnologyActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'sit_producttechnew/producttechnology/edit';
    const URL_PATH_DELETE = 'sit_producttechnew/producttechnology/delete';
    const URL_PATH_DUPLICATE = 'sit_producttechnew/producttechnology/duplicate';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * [__construct description]
     * @param ContextInterface   $context            [description]
     * @param UiComponentFactory $uiComponentFactory [description]
     * @param UrlInterface       $urlBuilder         [description]
     * @param array              $components         [description]
     * @param array              $data               [description]
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
            $storeId = $this->getContext()->getRequestParam('store');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    if (array_key_exists('technology_title', $item)) {
                        $techTitle = $item['technology_title'];
                    } else {
                        $techTitle = '';
                    }
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId]
                        ),
                        'label' => __('Edit'),
                        'hidden' => false,
                    ];
                    $item[$this->getData('name')]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DELETE,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ' . $techTitle),
                            'message' => __('Are you sure you wan\'t to delete a ' . $techTitle . ' record?'),
                        ],
                        'hidden' => false,
                    ];
                    $item[$this->getData('name')]['duplicate'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DUPLICATE,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId, 'duplicate' => 1]
                        ),
                        'label' => __('Duplicate'),
                        'hidden' => false,
                    ];

                }
            }
        }

        return $dataSource;
    }
}
