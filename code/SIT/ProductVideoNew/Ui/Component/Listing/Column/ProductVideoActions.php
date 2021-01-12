<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductVideoActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'sit_productvideonew/productvideo/edit';
    const URL_PATH_DELETE = 'sit_productvideonew/productvideo/delete';
    const URL_PATH_DUPLICATE = 'sit_productvideonew/productvideo/duplicate';

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
                            'title' => __('Delete ' . $item['video_title']),
                            'message' => __('Are you sure you wan\'t to delete a ' . $item['video_title'] . ' record?'),
                        ],
                        'hidden' => false,
                    ];
                    $item[$this->getData('name')]['duplicate'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DUPLICATE,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId]
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
