<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Preview extends Column
{
    protected $urlBuilder;

    protected $layout;

    protected $request;

    protected $_storeManager;

    public function __construct(
        ContextInterface $context,
        \Magento\Framework\Url $urlBuilder,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
        $this->request = $request;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    if (isset($item['url_key'])){
                        $item[$this->getData('name')] = [
                            'preview' => [
                                'href' => $this->urlBuilder->getUrl('generalnews/news/check',['news_id'=>$item['entity_id']]),
                                'target' => '_blank',
                                'label' => __('View')
                            ]
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get store URL
     *
     * @return  string
     */
    public function getStoreUrl()
    {
        $storeId = (int) $this->request->getParam('store',0);
        if ($storeId) {
            return $this->_storeManager->getStore($storeId)->getBaseUrl();
        }else
            return $this->_storeManager->getStore()->getBaseUrl();
    }

}