<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [22-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\CPU;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory;

class GridToCsv extends Action
{

    /**
     * sit_productcompatibility_productcompatibility_product
     */
    const SIT_PRODUCTCOMPATIBILITY_PRODUCTCOMPATIBILITY_PRODUCT = 'sit_productcompatibility_productcompatibility_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * FileFactory
     */
    protected $fileFactory;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * @var \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory
     */
    protected $templatetextFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context                                  $context             [description]
     * @param CollectionFactory                                                    $collectionFactory   [description]
     * @param FileFactory                                                          $fileFactory         [description]
     * @param ProductCompHelper                                                    $prodCompHelper      [description]
     * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CollectionFactory $collectionFactory,
        FileFactory $fileFactory,
        ProductCompHelper $prodCompHelper,
        \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;
        $this->prodCompHelper = $prodCompHelper;
        $this->templatetextFactory = $templatetextFactory;
        parent::__construct($context);
    }

    /**
     * generate csv file
     *
     * @return csv file
     */
    public function execute()
    {
        /**
         * For selected row export functionality [MD][START][11-05-2019]
         */
        $jobData = $this->collectionFactory->create();

        foreach ($jobData as $value) {
            $locatorId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams();
        $selectedAppsid = '';
        /**
         * For filter's data export [MD][START][11-05-2019]
         */
        $filters = $parameterData['filters'];
        unset($filters['placeholder']);
        $fields = [];
        foreach ($filters as $key => $value) {
            $fields[$key] = $value;
        }
        if (empty($fields)) {
            $filters = 0;
        }
        /**
         * For selected row export functionality [MD][START][11-05-2019]
         */
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $locatorId;
            } else {
                $selectedAppsid = array_diff($locatorId, $parameterData['excluded']);
            }
        }
        /**
         * For selected row export functionality [MD][END][11-05-2019]
         */
        $this->_view->loadLayout(false);
        $fileName = "ProductCompatibility_Cpu(" . date('Y-m-d') . ").csv";
        $storeId = $this->getRequest()->getParams();
        $compType = $this->getRequest()->getParam('comptype');
        if (array_key_exists('store_id', $storeId["filters"])) {
            $exportBlock = $this->collectionFactory->create()->setStoreId($storeId["filters"]['store_id'])->addFieldToSelect('*');
        } else {
            $exportBlock = $this->collectionFactory->create()->setStoreId(0)->addFieldToSelect('*');
        }
        /**
         * For selected row export functionality [MD][START][11-05-2019]
         */
        if ($selectedAppsid != 'false') {
            $exportBlock->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
        }
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        $optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($compType));
        $exportBlock->addAttributeToFilter('comp_type', $optionId);

        $socketList = $this->getAttributeLabelList(ProductCompHelper::COMP_SOCKET);
        $manufacturerList = $this->getAttributeLabelList(ProductCompHelper::COMP_MANUFACTURE);
        $modelList = $this->getAttributeLabelList(ProductCompHelper::COMP_MODEL);
        $seriesList = $this->getAttributeLabelList(ProductCompHelper::COMP_SERIES);
        $compList = $this->getAttributeLabelList(ProductCompHelper::COMP_VALUE);

        /**
         * START : Join Query For Add Product Name : MD
         */
        $exportBlock->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTCOMPATIBILITY_PRODUCTCOMPATIBILITY_PRODUCT],
            'e.entity_id = t2.productcompatibility_id',
            't2.product_id'
        );
        $exportBlock->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.value SEPARATOR \',\'),\',\',4) as pname'
        );
        $exportBlock->getSelect()->group('e.entity_id');
        /**
         * END : Join Query For Add Product Name : MD
         */

        /**
         * For selected row export functionality [MD][END][11-05-2019]
         */
        /**
         * For filter export functionality [MD][START][11-05-2019]
         */
        if ($filters != 0) {
            foreach ($fields as $fieldName => $value) {
                if ($fieldName != 'store_id') {
                    if ($fieldName == 'entity_id') {
                        $exportBlock->addFieldToFilter($fieldName, ["from" => $value['from'], "to" => $value['to']]);
                    }
                    if ($fieldName == 'product_name') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName != 'entity_id' && $fieldName != 'product_name') {
                        $modelInfo = null;
                        switch ($fieldName) {
                            case ProductCompHelper::COMP_SOCKET:
                                $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
                                break;
                            case ProductCompHelper::COMP_MANUFACTURE:
                                $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
                                break;
                            case ProductCompHelper::COMP_MODEL:
                                $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
                                break;
                            case ProductCompHelper::COMP_SERIES:
                                $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SERIES);
                                break;
                        }
                        if ($modelInfo) {
                            $compmodelId = $modelInfo->getAttributeId();
                            $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($compmodelId);
                            $ids = $this->prodCompHelper->compareArrayValues($modelAllOption, $value);
                            $exportBlock->addAttributeToFilter($fieldName, ['in' => $ids]);
                        } else {
                            if ($fieldName == 'template_text') {
                                $exportBlock->addAttributeToFilter(
                                    [
                                        ['attribute' => 'template_text_1', 'like' => $value],
                                        ['attribute' => 'template_text_2', 'like' => $value],
                                        ['attribute' => 'template_text_3', 'like' => $value],
                                        ['attribute' => 'template_text_4', 'like' => $value],
                                    ]
                                );
                            } else {
                                $exportBlock->addFieldToFilter($fieldName, ['like' => '%' . $value . '%']);
                            }
                        }
                    }
                }
            }
        }
        /**
         * For filter export functionality [MD][END][11-05-2019]
         */
        $this->_fileFactory = $this->fileFactory;
        $csvData = "entity_id,comp_socket,comp_manufacture,comp_model,comp_series,comp_value,status,products,template_text\n";
        $store_id = $this->getRequest()->getParam('store_id');
        foreach ($exportBlock as $key => $value) {
            $name = '';
            if ($value['template_text_1'] != '-1') {
                $templateText1 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_1']])->getFirstItem();
                $name .= $templateText1->getTemplateTextTitle() . " (T1),";
            }
            if ($value['template_text_2'] != '-1') {
                $templateText2 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_2']])->getFirstItem();
                $name .= $templateText2->getTemplateTextTitle() . " (T2),";
            }
            if ($value['template_text_3'] != '-1') {
                $templateText3 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_3']])->getFirstItem();
                $name .= $templateText3->getTemplateTextTitle() . " (T3),";
            }
            if ($value['template_text_4'] != '-1') {
                $templateText4 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_4']])->getFirstItem();
                $name .= $templateText4->getTemplateTextTitle() . " (T4),";
            }
            $tempName = rtrim($name, ', ');
            $status = $value->getStatus() == "1" ? "Enabled" : "Disabled";
            if (array_key_exists($value->getCompSocket(), $socketList)) {
                $socketValue = $socketList[$value->getCompSocket()];
            } else {
                $socketValue = '';
            }
            if (array_key_exists($value->getCompManufacture(), $manufacturerList)) {
                $manufacturerValue = $manufacturerList[$value->getCompManufacture()];
            } else {
                $manufacturerValue = '';
            }
            if (array_key_exists($value->getCompModel(), $modelList)) {
                $modelValue = $modelList[$value->getCompModel()];
            } else {
                $modelValue = '';
            }
            if (array_key_exists($value->getCompSeries(), $seriesList)) {
                $seriesValue = $seriesList[$value->getCompSeries()];
            } else {
                $seriesValue = '';
            }
            if (array_key_exists($value->getCompValue(), $compList)) {
                $compValue = $compList[$value->getCompValue()];
            } else {
                $compValue = '';
            }
            $csvData .= $value->getEntityId() . ',"' . $socketValue . '","' . $manufacturerValue . '","' . $modelValue . '","' . $seriesValue . '","' . $compValue . '",' . $status . ',"' . $value->getPname() . '","' . $tempName . '"' . "\n";
        }
        return $this->_fileFactory->create(
            $fileName,
            $csvData,
            DirectoryList::VAR_DIR
        );
    }
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
