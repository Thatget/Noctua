<?php
/**
 *
 * Maghos_ProductViewLinks Magento 2 extension
 *
 * NOTICE OF LICENSE
 *
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @category Maghos
 * @package Maghos_ProductViewLinks
 * @copyright Copyright (c) 2018 Maghos s.r.o.
 * @license http://www.maghos.com/licenses/license-1.html
 * @author Magento dev team <support@maghos.eu>
 */
namespace Maghos\ProductViewLinks\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Store\Model\StoreManagerInterface;
use Maghos\ProductViewLinks\Helper\Url;

class ProductView extends Column
{

    /** @var Url */
    private $helper;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Url $helper
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Url $helper,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->helper       = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $store     = $this->getStore();
            $storeId   = $store->getId();
            $storeCode = $store->getCode();
            $key       = $this->getData('name');
            $title     = __('View');

            foreach ($dataSource['data']['items'] as &$item) {
                $link = $this->helper->getProductUrl($item['entity_id'], $storeId, $storeCode);
                $html = '<a href="' . $link . '" target="_blank" onclick="window.open(this.href)">' . $title . '</a>';
                $item[$key] = $html;
            }
        }

        return $dataSource;
    }

    /**
     * Get current store
     *
     * @return \Magento\Store\Api\Data\StoreInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStore()
    {
        $storeId   = $this->context->getFilterParam('store_id');
        if ($storeId) {
            return $this->storeManager->getStore($storeId);
        } else {
            return $this->storeManager->getDefaultStoreView();
        }
    }
}
