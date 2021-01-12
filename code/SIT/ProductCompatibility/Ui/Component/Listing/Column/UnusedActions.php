<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\Listing\Column;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class UnusedActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * [__construct description]
     * @param ContextInterface                                   $context            [description]
     * @param UiComponentFactory                                 $uiComponentFactory [description]
     * @param UrlInterface                                       $urlBuilder         [description]
     * @param RequestInterface                                   $request            [description]
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession        [description]
     * @param array                                              $components         [description]
     * @param array                                              $data               [description]
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,

        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->coreSession = $coreSession;
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
        $editUrl = 'sit_productcompatibility/' . $this->coreSession->getProCompType() . '/edit';
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $editUrl,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId]
                        ),
                        'label' => __('Edit'),
                        'hidden' => false,
                    ];
                }
            }
        }
        return $dataSource;
    }
}
