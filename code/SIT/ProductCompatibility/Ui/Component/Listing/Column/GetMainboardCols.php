<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\Listing\Column;

use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

class GetMainboardCols extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory
     */
    protected $productCompCollFactory;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface                         $context                [description]
     * @param \Magento\Framework\View\Element\UiComponentFactory                                   $uiComponentFactory     [description]
     * @param \Magento\Framework\App\Request\Http                                                  $request                [description]
     * @param \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $productCompCollFactory [description]
     * @param ProductCompHelper                                                                    $prodCompHelper         [description]
     * @param array                                                                                $components             [description]
     * @param array                                                                                $data                   [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\App\Request\Http $request,
        \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $productCompCollFactory,
        ProductCompHelper $prodCompHelper,
        array $components = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->productCompCollFactory = $productCompCollFactory;
        $this->prodCompHelper = $prodCompHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            // foreach ($dataSource['data']['items'] as $key => $item) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['comp_socket']) && trim($item['comp_socket']) != "") {
                    $socketAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
                    $socketAttrId = $socketAttrInfo->getAttributeId();
                    $socketoption = $this->prodCompHelper->getAttrOptionLabel($socketAttrId, $item['comp_socket'])->getFirstItem();
                    $item['comp_socket'] = $socketoption['value'];
                }
                if (isset($item['comp_manufacture']) && trim($item['comp_manufacture']) != "") {
                    $manufInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
                    $manufAttrId = $manufInfo->getAttributeId();
                    $manufoption = $this->prodCompHelper->getAttrOptionLabel($manufAttrId, $item['comp_manufacture'])->getFirstItem();
                    $item['comp_manufacture'] = $manufoption['value'];
                }
                if (isset($item['comp_model']) && trim($item['comp_model']) != "") {
                    $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
                    $modelAttrId = $modelInfo->getAttributeId();
                    $modeloption = $this->prodCompHelper->getAttrOptionLabel($modelAttrId, $item['comp_model'])->getFirstItem();
                    $item['comp_model'] = $modeloption['value'];
                }
                if (isset($item['comp_series']) && trim($item['comp_series']) != "") {
                    $seriesInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SERIES);
                    $seriesAttrId = $seriesInfo->getAttributeId();
                    $seriesoption = $this->prodCompHelper->getAttrOptionLabel($seriesAttrId, $item['comp_series'])->getFirstItem();
                    $item['comp_series'] = $seriesoption['value'];
                }
                if (isset($item['comp_value']) && trim($item['comp_value']) != "") {
                    $compValAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_VALUE);
                    $compValAttrId = $compValAttrInfo->getAttributeId();
                    $compValoption = $this->prodCompHelper->getAttrOptionLabel($compValAttrId, $item['comp_value'])->getFirstItem();
                    $item['comp_value'] = $compValoption['value'];
                }
                if (isset($item['comp_type']) && trim($item['comp_type']) != "") {
                    $compTypeAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
                    $compTypeAttrId = $compTypeAttrInfo->getAttributeId();
                    $compTypeoption = $this->prodCompHelper->getAttrOptionLabel($compTypeAttrId, $item['comp_type'])->getFirstItem();
                    $item['comp_type'] = $compTypeoption['value'];
                }
            }
        }
        return $dataSource;
    }
}
