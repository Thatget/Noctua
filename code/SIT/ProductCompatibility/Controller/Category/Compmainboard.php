<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Category;

use Magento\Framework\Controller\ResultFactory;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

class Compmainboard extends \Magento\Framework\App\Action\Action
{

    const CATEGORY_URL_KEY = 'products';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \SIT\ProductCompatibility\Model\ProductCompatibilityFactory
     */
    protected $productCompFactory;

    /**
     * @var \SIT\ProductCompatibility\Helper\Data
     */
    protected $prodCompHelper;

    /**
     * @param \Magento\Framework\App\Action\Context                       $context            [description]
     * @param \Magento\Framework\View\Result\PageFactory                  $resultPageFactory  [description]
     * @param ProductCompHelper                                           $prodCompHelper     [description]
     * @param \SIT\ProductCompatibility\Model\ProductCompatibilityFactory $productCompFactory [description]
     * @param \Magento\Framework\Json\Helper\Data                         $jsonHelper         [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ProductCompHelper $prodCompHelper,
        \SIT\ProductCompatibility\Model\ProductCompatibilityFactory $productCompFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->prodCompHelper = $prodCompHelper;
        $this->productCompFactory = $productCompFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $paramArray = [];
        $paramArray = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
        $setModel = 0;
        $compSocketId = 0;
        if (array_key_exists('set_model', $paramArray)) {
            $setModel = 1;
        }
        if (array_key_exists('comp_socket_id', $paramArray)) {
            $compSocketId = $paramArray['comp_socket_id'];
        }
        $gridType = 'Mainboard';
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);

        $this->socketList = $this->getAttributeLabelList(ProductCompHelper::COMP_SOCKET);
        $this->manufacturerList = $this->getAttributeLabelList(ProductCompHelper::COMP_MANUFACTURE);
        $this->modelList = $this->getAttributeLabelList(ProductCompHelper::COMP_MODEL);
        $this->mainBoardUrl = $this->_url->getUrl('mainboard');

        $optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($gridType));
        $collection = $this->productCompFactory->create()->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('comp_type', ['eq' => $optionId])->setOrder('comp_socket', 'ASC');
        $allCollection = $collection;
        $collection->getSelect()->group('comp_socket');
        $allMainboardData = [];
        $collectionData = [];
        $this->mainCollection($optionId, $setModel, $compSocketId);
        if ($setModel == 1) {
            if ($compSocketId != 'all') {
                $collection->addAttributeToFilter('comp_socket', ['eq' => $compSocketId]);
            }
            foreach ($collection as $key => $value) {
                $collectionData['comp_socket'] = $value['comp_socket'];
                $collectionData['comp_socket_value'] = $this->socketList[$value['comp_socket']];
                $collectionData['comp_socket_manufacture'] = $this->reIndexArray(array_values($this->mainArray[$value['comp_socket']]));
                array_push($allMainboardData, $collectionData);
            }
        } else {
            foreach ($collection as $key => $value) {
                $collectionData['comp_socket'] = $value['comp_socket'];
                $collectionData['comp_socket_value'] = $this->socketList[$value['comp_socket']];
                $collectionData['comp_socket_manufacture'] = $this->reIndexArrayData(array_values($this->mainArray[$value['comp_socket']]));
                array_push($allMainboardData, $collectionData);
            }
        }
        usort($allMainboardData, function ($item1, $item2) {
            return strtolower($item1['comp_socket_value']) <=> strtolower($item2['comp_socket_value']);
        });

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode($allMainboardData));
        return $response;
    }

    /**
     * Change key of index
     * @return Array
     */
    public function reIndexArray($data)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            $value['comp_models'] = array_values($value['comp_models']);
            array_push($newData, $value);
        }
        return $newData;
    }

    public function reIndexArrayData($data)
    {
        usort($data, function ($a, $b) {
            return strtolower($a['comp_manufacture_value']) <=> strtolower($b['comp_manufacture_value']);
        });

        return $data;
    }

    /**
     * Prepare main collection
     * @param  [type] $optionId [description]
     * @return null
     */
    public function mainCollection($optionId, $setModel, $compSocketId)
    {
        if ($setModel == 1) {
            $collection = $this->productCompFactory->create()->getCollection()
                ->addAttributeToSelect(['comp_socket', 'comp_model', 'comp_manufacture', 'comp_type', 'url_key'])
                ->addAttributeToFilter('comp_type', ['eq' => $optionId])->setOrder('comp_model', 'ASC');
            if ($compSocketId != 'all') {
                $collection->addAttributeToFilter('comp_socket', ['eq' => $compSocketId]);
            }
            $this->mainArray = [];
            foreach ($collection as $value) {
                if (array_key_exists($value['comp_model'], $this->modelList)) {
                    $this->mainArray[$value['comp_socket']][$value['comp_manufacture']]["comp_manufacture"] = $value['comp_manufacture'];
                    $this->mainArray[$value['comp_socket']][$value['comp_manufacture']]["comp_manufacture_value"] = $this->manufacturerList[$value['comp_manufacture']];
                    $this->mainArray[$value['comp_socket']][$value['comp_manufacture']]['comp_models'][$value['comp_model']] = [
                        'comp_model' => $value['comp_model'],
                        'comp_model_value' => $this->modelList[$value['comp_model']],
                        'url_key' => $this->mainBoardUrl . $value['url_key'],
                    ];
                }
            }
        } else {
            $collection = $this->productCompFactory->create()->getCollection()
                ->addAttributeToSelect(['comp_socket', 'comp_manufacture', 'comp_type'])
                ->addAttributeToFilter('comp_type', ['eq' => $optionId])->setOrder('comp_model', 'ASC');
            foreach ($collection as $value) {
                $this->mainArray[$value['comp_socket']][$value['comp_manufacture']]["comp_manufacture"] = $value['comp_manufacture'];
                $this->mainArray[$value['comp_socket']][$value['comp_manufacture']]["comp_manufacture_value"] = $this->manufacturerList[$value['comp_manufacture']];

            }
        }
    }

    /**
     * Get value of attribute
     * @param  [type] $attributeCode [description]
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
