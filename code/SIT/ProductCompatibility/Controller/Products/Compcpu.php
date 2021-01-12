<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Products;

use Magento\Framework\Controller\ResultFactory;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

class Compcpu extends \Magento\Framework\App\Action\Action
{
    /**
     * Product Compatibility Product Table Name
     */
    const PRODUCT_TABLE = 'sit_productcompatibility_productcompatibility_product';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * @var \SIT\ProductCompatibility\Model\ProductCompatibilityFactory
     */
    protected $productCompFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory
     */
    protected $templatetextFactory;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context                                $context             [description]
     * @param \Magento\Framework\View\Result\PageFactory                           $resultPageFactory   [description]
     * @param ProductCompHelper                                                    $prodCompHelper      [description]
     * @param \SIT\ProductCompatibility\Model\ProductCompatibilityFactory          $productCompFactory  [description]
     * @param \Magento\Framework\Json\Helper\Data                                  $jsonHelper          [description]
     * @param \Magento\Store\Model\StoreManagerInterface                           $storeManager        [description]
     * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ProductCompHelper $prodCompHelper,
        \SIT\ProductCompatibility\Model\ProductCompatibilityFactory $productCompFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->prodCompHelper = $prodCompHelper;
        $this->productCompFactory = $productCompFactory;
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->templatetextFactory = $templatetextFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $gridType = ProductCompHelper::COMP_CPU;
        /**
         * $setModel used for identified request
         * $setModel = 0 Then data return for only list
         * $setModel = 1 Then data return for only grid
         */
        $setModel = 0;
        $allCPUData = [];
        $collectionData = [];
        $compManufactureId = 0;
        $paramArray = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
        if (array_key_exists('set_model', $paramArray)) {
            $setModel = 1;
        }
        if (array_key_exists('comp_manufacturer_id', $paramArray)) {
            $compManufactureId = $paramArray['comp_manufacturer_id'];
        }
        /**
         * Set all values(labels) of attributes
         */
        $this->socketList = $this->getAttributeLabelList(ProductCompHelper::COMP_SOCKET);
        $this->manufacturerList = $this->getAttributeLabelList(ProductCompHelper::COMP_MANUFACTURE);
        $this->modelList = $this->getAttributeLabelList(ProductCompHelper::COMP_MODEL);
        $this->seriesList = $this->getAttributeLabelList('comp_series');
        $this->compvalueList = $this->getAttributeLabelList(ProductCompHelper::COMP_VALUE);

        /**
         * Set custom route url
         */
        $this->cpuUrl = $this->_url->getUrl('cpu');

        $storeId = $this->storeManager->getStore()->getId();
        $currentProductId = $paramArray['productID'];
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        $optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($gridType));
        $collection = $this->productCompFactory->create()->setStoreId($storeId)->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('comp_type', ['eq' => $optionId])->addAttributeToFilter('status', ['eq' => 1])->setOrder('comp_manufacture', 'ASC');
        $collection->getSelect()->group('comp_manufacture');

        $collection->getSelect()->joinLeft(
            ['productTable' => self::PRODUCT_TABLE],
            'e.entity_id = productTable.productcompatibility_id',
            ['productTable.product_id']
        )->where('productTable.product_id = ' . $currentProductId);
        $this->mainCollection($optionId, $setModel, $storeId, $currentProductId, $compManufactureId);
        if ($setModel == 1) {
            foreach ($collection as $key => $value) {
                if (array_key_exists($value['comp_manufacture'], $this->mainArray)) {
                    $collectionData['comp_manufacture'] = $value['comp_manufacture'];
                    $collectionData['comp_manufacture_value'] = $this->manufacturerList[$value['comp_manufacture']];
                    $this->mainArray[$value['comp_manufacture']]['series'] = $this->checkSeries(array_values($this->mainArray[$value['comp_manufacture']]['series']));

                    $this->mainArray[$value['comp_manufacture']]['socket'] = $this->reIndexArray(array_values($this->mainArray[$value['comp_manufacture']]['socket']));

                    $collectionData['comp_socket_manufacture'] = $this->mainArray[$value['comp_manufacture']];
                    array_push($allCPUData, $collectionData);
                }
            }
        } else {
            foreach ($collection as $key => $value) {
                if (array_key_exists($value['comp_manufacture'], $this->mainArray)) {
                    $collectionData['comp_manufacture'] = $value['comp_manufacture'];
                    $collectionData['comp_manufacture_value'] = $this->manufacturerList[$value['comp_manufacture']];
                    $this->mainArray[$value['comp_manufacture']]['series'] = $this->checkSeries(array_values($this->mainArray[$value['comp_manufacture']]['series']));

                    $this->mainArray[$value['comp_manufacture']]['socket'] = array_values($this->mainArray[$value['comp_manufacture']]['socket']);

                    $collectionData['comp_socket_manufacture'] = $this->mainArray[$value['comp_manufacture']];
                    array_push($allCPUData, $collectionData);
                }
            }
        }

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode($allCPUData));
        return $response;
    }

    /**
     * Check duplicate values in series array and if found duplicate than append socket name
     * @param  Array
     * @return Array
     */
    public function checkSeries($seriesArray)
    {
        $seriesValues = [];
        foreach ($seriesArray as $key => $series) {
            if (!in_array($series['comp_series_value'], $seriesValues)) {
                $seriesValues[] = $series['comp_series_value'];
            } else {
                $newSeries = $seriesArray[$key]['comp_series_value'] . ' (' . $this->socketList[$seriesArray[$key]['comp_socket']] . ')';
                $seriesArray[$key]['comp_series_value'] = $newSeries;
            }
        }
        return $seriesArray;
    }

    /**
     * reset index of array
     * @param  Array
     * @return Array
     */
    public function reIndexArray($data)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            $value['comp_models'] = array_values($value['comp_models']);
            usort($value['comp_models'], function ($a, $b) {
                return strtolower($a['comp_series_value']) <=> strtolower($b['comp_series_value']);
            });
            array_push($newData, $value);
        }

        return $newData;
    }

    /**
     * Set main collecion of cpu tab
     * @param  int
     * @return
     */
    public function mainCollection($optionId, $setModel, $storeId, $currentProductId, $compManufactureId)
    {
        $templateText = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addAttributeToFilter('status', ['eq' => 1]);
        $templateTextArray = [];
        $templateTextArray[-1] = '';
        $this->mainArray = [];

        foreach ($templateText as $key => $value) {
            $templateTextArray[$value['entity_id']] = $value['template_text_comment'];
        }
        if ($setModel == 1) {
            $collection = $this->productCompFactory->create()->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('comp_type', ['eq' => $optionId])
                ->addAttributeToFilter('status', ['eq' => 1])
                ->setOrder('comp_model', 'ASC');
            if ($compManufactureId != 'all') {
                $collection->addAttributeToFilter('comp_manufacture', ['eq' => $compManufactureId]);
            }
            $collection->getSelect()->joinLeft(
                ['productTable' => self::PRODUCT_TABLE],
                'e.entity_id = productTable.productcompatibility_id',
                ['productTable.product_id']
            )->where('productTable.product_id = ' . $currentProductId);
            foreach ($collection as $value) {
                $extra_comment = '';
                $compatibility = '';
                if (array_key_exists($value['comp_model'], $this->modelList) && array_key_exists($value['comp_socket'], $this->socketList)) {
                    $this->mainArray[$value['comp_manufacture']]["socket"][$value['comp_socket']]["comp_socket"] = $value['comp_socket'];
                    $this->mainArray[$value['comp_manufacture']]["socket"][$value['comp_socket']]["comp_socket_value"] = $this->socketList[$value['comp_socket']];
                    $this->mainArray[$value['comp_manufacture']]["series"]["socket_" . $value['comp_socket'] . "_series_" . $value['comp_series']]["comp_series"] = $value['comp_series'];
                    $this->mainArray[$value['comp_manufacture']]["series"]["socket_" . $value['comp_socket'] . "_series_" . $value['comp_series']]["comp_socket"] = $value['comp_socket'];
                    $this->mainArray[$value['comp_manufacture']]["series"]["socket_" . $value['comp_socket'] . "_series_" . $value['comp_series']]["comp_series_value"] = $this->seriesList[$value['comp_series']];
                    $comment = $value['comp_extra_comment'];
                    if (array_key_exists($value['template_text_1'], $templateTextArray)) {
                        $extra_comment .= $templateTextArray[$value['template_text_1']];
                    }
                    if (array_key_exists($value['template_text_2'], $templateTextArray)) {
                        $extra_comment .= $templateTextArray[$value['template_text_2']];
                    }
                    if (array_key_exists($value['template_text_3'], $templateTextArray)) {
                        $extra_comment .= $templateTextArray[$value['template_text_3']];
                    }
                    if (array_key_exists($value['template_text_4'], $templateTextArray)) {
                        $extra_comment .= $templateTextArray[$value['template_text_4']];
                    }

                    if ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_COMPATIBLE && trim($comment) == '' && $extra_comment == '') {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'g1.png');
                    } elseif ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_COMPATIBLE && trim($comment) != '' || $extra_comment != '') {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'b1.png');
                    }

                    if ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_INCOMPATIBLE) {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'r1.png');
                    } elseif ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_OC1) {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'oc1.png');
                    } elseif ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_OC2) {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'oc2.png');
                    } elseif ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_OC3) {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'oc3.png');
                    }

                    $this->mainArray[$value['comp_manufacture']]["socket"][$value['comp_socket']]['comp_models'][$value['comp_model']] = [
                        'comp_series' => $value['comp_series'],
                        'entity_id' => $value['entity_id'],
                        'comp_model_value' => $this->modelList[$value['comp_model']],
                        'comp_comment' => "<ul>" . $extra_comment . $comment . "</ul>",
                        'comp_compatibility' => $compatibility,
                        'comp_series_value' => $this->seriesList[$value['comp_series']],
                        'url_key' => $this->cpuUrl . $value['url_key'],
                    ];
                }
            }
        } else {
            $this->mainArray = [];
            $collection = $this->productCompFactory->create()->getCollection()
                ->addAttributeToSelect(['comp_model', 'comp_socket', 'comp_manufacture', 'comp_series'])
                ->addAttributeToFilter('comp_type', ['eq' => $optionId])
                ->addAttributeToFilter('status', ['eq' => 1])
                ->setOrder('comp_model', 'ASC');
            $collection->getSelect()->joinLeft(
                ['productTable' => self::PRODUCT_TABLE],
                'e.entity_id = productTable.productcompatibility_id',
                ['productTable.product_id']
            )->where('productTable.product_id = ' . $currentProductId);
            foreach ($collection as $value) {
                if (array_key_exists($value['comp_model'], $this->modelList) && array_key_exists($value['comp_socket'], $this->socketList)) {
                    $this->mainArray[$value['comp_manufacture']]["socket"][$value['comp_socket']]["comp_socket"] = $value['comp_socket'];
                    $this->mainArray[$value['comp_manufacture']]["socket"][$value['comp_socket']]["comp_socket_value"] = $this->socketList[$value['comp_socket']];
                    $this->mainArray[$value['comp_manufacture']]["series"]["socket_" . $value['comp_socket'] . "_series_" . $value['comp_series']]["comp_series"] = $value['comp_series'];
                    $this->mainArray[$value['comp_manufacture']]["series"]["socket_" . $value['comp_socket'] . "_series_" . $value['comp_series']]["comp_socket"] = $value['comp_socket'];
                    $this->mainArray[$value['comp_manufacture']]["series"]["socket_" . $value['comp_socket'] . "_series_" . $value['comp_series']]["comp_series_value"] = $this->seriesList[$value['comp_series']];
                }
            }
        }
    }

    /**
     * Get attribute options by code
     * @param  string
     * @return Array
     */
    public function getAttributeLabelList($attributeCode)
    {
        $options = [];
        $socketInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, $attributeCode);
        $socketId = $socketInfo->getAttributeId();
        $socketAllOption = $this->prodCompHelper->getAttributeOptionAll($socketId);
        foreach ($socketAllOption as $key => $item) {
            $options[$item->getOptionId()] = $item->getValue();
        }
        return $options;
    }
}
