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

class Compcase extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory
     */
    protected $templatetextFactory;

    /**
     * @param \Magento\Framework\App\Action\Context                       $context            [description]
     * @param \Magento\Framework\View\Result\PageFactory                  $resultPageFactory  [description]
     * @param ProductCompHelper                                           $prodCompHelper     [description]
     * @param \SIT\ProductCompatibility\Model\ProductCompatibilityFactory $productCompFactory [description]
     * @param \Magento\Framework\Json\Helper\Data                         $jsonHelper         [description]
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager       [description]
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
        $this->jsonHelper = $jsonHelper;
        $this->prodCompHelper = $prodCompHelper;
        $this->productCompFactory = $productCompFactory;
        $this->storeManager = $storeManager;
        $this->templatetextFactory = $templatetextFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $gridType = ProductCompHelper::COMP_CASE;
        /**
         * $setModel used for identified request
         * $setModel = 0 Then data return for only list
         * $setModel = 1 Then data return for only grid
         */
        $setModel = 0;
        $allCaseData = [];
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
        $this->compvalueList = $this->getAttributeLabelList(ProductCompHelper::COMP_VALUE);

        /**
         * Set custom route url
         */
        $this->caseUrl = $this->_url->getUrl('case');

        $storeId = $this->storeManager->getStore()->getId();
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        $optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($gridType));
        $collection = $this->productCompFactory->create()->setStoreId($storeId)->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('comp_type', ['eq' => $optionId])->setOrder('comp_manufacture', 'ASC');
        $allCollection = $collection;
        $collection->getSelect()->group('comp_manufacture');

        $this->mainCollection($optionId, $setModel, $storeId, $compManufactureId);
        if ($setModel == 1) {
            if ($compManufactureId != 'all') {
                $collection->addAttributeToFilter('comp_manufacture', ['eq' => $compManufactureId]);
            }
            foreach ($collection as $key => $value) {
                $collectionData['comp_manufacture'] = $value['comp_manufacture'];
                $collectionData['comp_manufacture_value'] = $this->manufacturerList[$value['comp_manufacture']];
                $collectionData['comp_manufacture_manufacture'] = array_values($this->mainArray[$value['comp_manufacture']]);
                array_push($allCaseData, $collectionData);
            }
        } else {
            foreach ($collection as $key => $value) {
                $collectionData['comp_manufacture'] = $value['comp_manufacture'];
                $collectionData['comp_manufacture_value'] = $this->manufacturerList[$value['comp_manufacture']];
                $collectionData['comp_manufacture_manufacture'] = array_values($this->mainArray[$value['comp_manufacture']]);
                if (count($collectionData['comp_manufacture_manufacture']) >= 1) {
                    if ($collectionData['comp_manufacture_manufacture'][0]['max_cooler_height'] == null) {
                        $collectionData['is_show'] = 0;
                    } else {
                        $collectionData['is_show'] = 1;
                    }
                } else {
                    $collectionData['is_show'] = 1;
                }
                array_push($allCaseData, $collectionData);
            }
        }
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode($allCaseData));
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

    /**
     * Prepare main collection
     * @param  [type] $optionId [description]
     * @return null
     */
    public function mainCollection($optionId, $setModel, $storeId, $compManufactureId)
    {
        $templateText = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*');
        $templateTextArray = [];
        $templateTextArray[-1] = '';
        $this->mainArray = [];

        foreach ($templateText as $key => $value) {
            $templateTextArray[$value['entity_id']] = $value['template_text_comment'];
        }

        if ($setModel == 1) {
            $collection = $this->productCompFactory->create()->setStoreId($storeId)->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('comp_type', ['eq' => $optionId])->setOrder('comp_model', 'ASC');

            if ($compManufactureId != 'all') {
                $collection->addAttributeToFilter('comp_manufacture', ['eq' => $compManufactureId]);
            }
            foreach ($collection as $value) {
                $extra_comment = '';
                $compatibility = '';
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
                if (array_key_exists($value['comp_value'], $this->compvalueList) && array_key_exists($value['comp_model'], $this->modelList)) {
                    if ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_COMPATIBLE) {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'g1.png');
                    } elseif ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_PI) {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'b1.png');
                    } elseif ($this->compvalueList[$value["comp_value"]] == ProductCompHelper::COMP_TYPE_INCOMPATIBLE) {
                        $compatibility = $this->prodCompHelper->getImage('default/images/', 'r1.png');
                    }
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["comp_model"] = $value['comp_model'];
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["url_key"] = $this->caseUrl . $value['url_key'];
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["comp_model_value"] = $this->modelList[$value['comp_model']];
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["max_cooler_height"] = $value['max_cooler_height'];
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["comp_compatibility"] = $compatibility;
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["comment"] = $extra_comment . $comment;
                }
            }
        } else {
            $collection = $this->productCompFactory->create()->setStoreId($storeId)->getCollection()
                ->addAttributeToSelect(['comp_manufacture', 'comp_model', 'comp_type', 'max_cooler_height'])
                ->addAttributeToFilter('comp_type', ['eq' => $optionId])->setOrder('comp_model', 'ASC');
            foreach ($collection as $value) {
                if (array_key_exists($value['comp_model'], $this->modelList)) {
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["comp_model"] = $value['comp_model'];
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["comp_model_value"] = $this->modelList[$value['comp_model']];
                    $this->mainArray[$value['comp_manufacture']][$value['comp_model']]["max_cooler_height"] = $value['max_cooler_height'];
                }
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
        $socketInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, $attributeCode);
        $socketId = $socketInfo->getAttributeId();
        $socketAllOption = $this->prodCompHelper->getAttributeOptionAll($socketId);
        foreach ($socketAllOption as $key => $item) {
            $options[$item->getOptionId()] = $item->getValue();
        }
        return $options;
    }
}
