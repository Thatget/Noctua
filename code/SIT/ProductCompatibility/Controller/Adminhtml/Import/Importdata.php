<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [28-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\Product as SitProductComp;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;
use SIT\ProductCompatibility\Model\ProductFactory as SitProductCompFactory;

class Importdata extends \Magento\Backend\App\Action
{
    /**
     * @var ProductCompatibilityFactory
     */
    protected $productcompFactory;

    /**
     * @var SitProductComp
     */
    protected $sitProductCompProduct;

    /**
     * @var SitProductCompFactory
     */
    protected $sitProductCompFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $entityAttribute;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $attributeOptionCollectionFactory;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \SIT\TemplateText\Model\TemplateTextFactory
     */
    protected $templateCollectionFactory;

    /**
     * [__construct description]
     * @param Action\Context                                                             $context                          [description]
     * @param ProductCompatibilityFactory                                                $productcompFactory               [description]
     * @param SitProductComp                                                             $sitProductCompProduct            [description]
     * @param SitProductCompFactory                                                      $sitProductCompFactory            [description]
     * @param \Magento\Eav\Model\Entity\Attribute                                        $entityAttribute                  [description]
     * @param \Magento\Eav\Setup\EavSetupFactory                                         $eavSetupFactory                  [description]
     * @param \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute                $attributeFactory                 [description]
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory [description]
     * @param \Magento\Framework\File\Csv                                                $csvProcessor                     [description]
     * @param ProductCompHelper                                                          $prodCompHelper                   [description]
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory             $productCollectionFactory         [description]
     * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory       $templateCollectionFactory        [description]
     */
    public function __construct(
        Action\Context $context,
        ProductCompatibilityFactory $productcompFactory,
        SitProductComp $sitProductCompProduct,
        SitProductCompFactory $sitProductCompFactory,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        ProductCompHelper $prodCompHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templateCollectionFactory
    ) {
        parent::__construct($context);
        $this->productcompFactory = $productcompFactory;
        $this->sitProductCompProduct = $sitProductCompProduct;
        $this->sitProductCompFactory = $sitProductCompFactory;
        $this->entityAttribute = $entityAttribute;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeFactory = $attributeFactory;
        $this->attributeOptionCollectionFactory = $attributeOptionCollectionFactory;
        $this->csvProcessor = $csvProcessor;
        $this->prodCompHelper = $prodCompHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->templateCollectionFactory = $templateCollectionFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $compType = $this->getRequest()->getParam('comptype');
        $storeId = $this->getRequest()->getParam('store');
        $resultRedirect = $this->resultRedirectFactory->create();
        //Product name collection[START]
        $collectionData = $this->productCollectionFactory->create();
        $collectionData->addAttributeToSelect('name');
        $productsName = [];
        foreach ($collectionData as $key => $proName) {
            $productsName[] = $proName['name'];
        }
        //Product name collection[END]
        //Template Text Id Collection[START]
        $template = $this->templateCollectionFactory->create()->setStoreId(0)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1]);
        $templateIds = [];
        foreach ($template as $key => $item) {
            $templateIds[$key] = $item->getId();
        }
        //Template Text Id Collection[END]
        try {
            $fullPath = $data['csv_import'][0]['path'] . '/' . $data['csv_import'][0]['name'];
            $compData = $this->csvProcessor->getData($fullPath);
            $count = 0;
            foreach ($compData as $key => $dataRow) {
                $compList = array_combine($compData[0], array_values($dataRow));
                if ($key != 0) {
                    if ($compList['comp_socket'] == '' || $compList['comp_manufacture'] == '' || $compList['comp_model'] == '' || $compList['comp_value'] == '') {
                        if ($compList['entity_id']) {
                            $this->messageManager->addWarning(__('Please fill required fields of Entity Id => ' . $compList['entity_id'] . ' in CSV.'));
                        } else {
                            $this->messageManager->addWarning(__('Please fill required fields in CSV.'));
                        }
                        return $resultRedirect->setPath('*/*/import');
                    } else {
                        //Product Id array and check product name is exist or not[START]
                        if ($compList['products'] != '' && isset($compList['products']) && is_string($compList['products'])) {
                            $productsIds = [];
                            //Get product id by product name
                            $proName = explode(',', $compList['products']);
                            $checkProductExist = !array_diff($proName, $productsName);
                            if ($checkProductExist == 1) {
                                $collection = $this->productCollectionFactory->create();
                                $collection->addFieldToFilter('name', ['in' => $proName]);
                                foreach ($collection as $key => $value) {
                                    $productsIds[] = $value['entity_id'];
                                }
                            } else {
                                if ($compList['entity_id']) {
                                    $this->messageManager->addWarning(__('Please enter proper product names Entity Id => ' . $compList['entity_id'] . ' in CSV.'));
                                } else {
                                    $this->messageManager->addWarning(__('Please enter proper product names in CSV.'));
                                }
                                return $resultRedirect->setPath('*/*/import');
                            }
                        } else {
                            $productsIds = [];
                        }
                        //Product Id array and check product name is exist or not[END]
                        //Check template Id exist or not[START]
                        if (array_key_exists('template_text_1', $compList)) {
                            if ($compList['template_text_1'] != '' && $compList['template_text_1'] != -1) {
                                if (in_array($compList['template_text_1'], $templateIds)) {
                                } else {
                                    if ($compList['entity_id']) {
                                        $this->messageManager->addWarning(__('Template Text 1 => ' . $compList['template_text_1'] . ' not exist in Entity Id => ' . $compList['entity_id'] . ' in CSV.  Please enter proper Template Text Id.'));
                                    } else {
                                        $this->messageManager->addWarning(__('Template Text 1 => ' . $compList['template_text_1'] . ' not exist in CSV. Please enter proper Template Text Id.'));
                                    }
                                    return $resultRedirect->setPath('*/*/import');
                                }
                            }
                        }
                        if (array_key_exists('template_text_2', $compList)) {
                            if ($compList['template_text_2'] != '' && $compList['template_text_2'] != -1) {
                                if (in_array($compList['template_text_2'], $templateIds)) {
                                } else {
                                    if ($compList['entity_id']) {
                                        $this->messageManager->addWarning(__('Template Text 2 => ' . $compList['template_text_2'] . ' not exist in Entity Id => ' . $compList['entity_id'] . ' in CSV. Please enter proper Template Text Id.'));
                                    } else {
                                        $this->messageManager->addWarning(__('Template Text 2 => ' . $compList['template_text_2'] . ' not exist in CSV. Please enter proper Template Text Id.'));
                                    }
                                    return $resultRedirect->setPath('*/*/import');
                                }
                            }
                        }
                        if (array_key_exists('template_text_3', $compList)) {
                            if ($compList['template_text_3'] != '' && $compList['template_text_3'] != -1) {
                                if (in_array($compList['template_text_3'], $templateIds)) {
                                } else {
                                    if ($compList['entity_id']) {
                                        $this->messageManager->addWarning(__('Template Text 3 => ' . $compList['template_text_3'] . ' not exist in Entity Id => ' . $compList['entity_id'] . ' in CSV. Please enter proper Template Text Id.'));
                                    } else {
                                        $this->messageManager->addWarning(__('Template Text 3 => ' . $compList['template_text_3'] . ' not exist in CSV. Please enter proper Template Text Id.'));
                                    }
                                    return $resultRedirect->setPath('*/*/import');
                                }
                            }
                        }
                        if (array_key_exists('template_text_4', $compList)) {
                            if ($compList['template_text_4'] != '' && $compList['template_text_4'] != -1) {
                                if (in_array($compList['template_text_4'], $templateIds)) {
                                } else {
                                    if ($compList['entity_id']) {
                                        $this->messageManager->addWarning(__('Template Text 4 => ' . $compList['template_text_4'] . ' not exist in Entity Id => ' . $compList['entity_id'] . ' in CSV.'));
                                    } else {
                                        $this->messageManager->addWarning(__('Template Text 4 => ' . $compList['template_text_4'] . ' not exist in CSV.'));
                                    }
                                    return $resultRedirect->setPath('*/*/import');
                                }
                            }
                        }
                        //Check template Id exist or not[END]
                        $dataComp = $this->saveCompatibility($compList, $compType, $productsIds, $storeId);
                    }
                }
                $count++;
            }
            $this->messageManager->addSuccess(__('A total of %1 ' . $compType . ' Compatibilities updated.', $count - 1));
            $resultRedirect->setPath('*/*/import');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $resultRedirect->setPath('*/*/import');
        }
        return $resultRedirect;
    }

    protected function saveCompatibility($compData, $compType, $productsIds, $storeId)
    {
        if ($compData) {
            $productcompInstance = $this->productcompFactory->create();
            $productcompInstance->setStoreId($storeId);
            $urlKey = str_replace(' ', '_', $compData['comp_manufacture']) . "_" . str_replace(' ', '_', $compData['comp_model']);
            if (empty($compData['entity_id'])) {
                $compData["url_key"] = $urlKey;
                $compData['entity_id'] = null;
            } else {
                $productcompInstance->load($compData['entity_id']);
                $checkUrl = $this->checkUrlKey($compData['entity_id'], $urlKey);
                if ($checkUrl) {
                    $compData["url_key"] = $urlKey;
                }
            }

            $socketInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
            $socketId = $socketInfo->getAttributeId();
            $socketAllOption = $this->prodCompHelper->getAttributeOptionAll($socketId);

            $manufInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
            $manufId = $manufInfo->getAttributeId();
            $manufAllOption = $this->prodCompHelper->getAttributeOptionAll($manufId);

            $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
            $modelId = $modelInfo->getAttributeId();
            $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);

            $compValInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_VALUE);
            $compValId = $compValInfo->getAttributeId();
            $compValAllOption = $this->prodCompHelper->getAttributeOptionAll($compValId);

            $compTypeInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
            $compTypeId = $compTypeInfo->getAttributeId();

            $socketAttrCheck = $this->checkAttrExist($socketAllOption, ProductCompHelper::COMP_SOCKET, trim($compData['comp_socket']));
            $manufAttrCheck = $this->checkAttrExist($manufAllOption, ProductCompHelper::COMP_MANUFACTURE, trim($compData['comp_manufacture']));
            $modelAttrCheck = $this->checkAttrExist($modelAllOption, ProductCompHelper::COMP_MODEL, trim($compData['comp_model']));
            $compValAttrCheck = $this->checkAttrExist($compValAllOption, ProductCompHelper::COMP_VALUE, trim($compData['comp_value']));

            $socket_after_save = $this->prodCompHelper->getAttributeOptionAll($socketId);
            $manuf_after_save = $this->prodCompHelper->getAttributeOptionAll($manufId);
            $model_after_save = $this->prodCompHelper->getAttributeOptionAll($modelId);
            $compVal_after_save = $this->prodCompHelper->getAttributeOptionAll($compValId);
            $compType_after_save = $this->prodCompHelper->getAttributeOptionAll($compTypeId);

            $compData['comp_socket'] = $this->prodCompHelper->getAttrOptionId($socket_after_save, trim($compData['comp_socket']));
            $compData['comp_manufacture'] = $this->prodCompHelper->getAttrOptionId($manuf_after_save, trim($compData['comp_manufacture']));
            $compData['comp_model'] = $this->prodCompHelper->getAttrOptionId($model_after_save, trim($compData['comp_model']));
            $compData['comp_value'] = $this->prodCompHelper->getAttrOptionId($compVal_after_save, trim($compData['comp_value']));
            $compData['comp_type'] = $this->prodCompHelper->getAttrOptionId($compType_after_save, trim($compType));

            //For CPU Compatibility[START]
            if ($compType == 'CPU') {
                $seriesInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SERIES);
                $seriesId = $seriesInfo->getAttributeId();
                $seriesAllOption = $this->prodCompHelper->getAttributeOptionAll($seriesId);
                $seriesAttrCheck = $this->checkAttrExist($seriesAllOption, ProductCompHelper::COMP_SERIES, trim($compData['comp_series']));
                $series_after_save = $this->prodCompHelper->getAttributeOptionAll($seriesId);
                $compData['comp_series'] = $this->prodCompHelper->getAttrOptionId($series_after_save, trim($compData['comp_series']));
            }
            //For CPU Compatibility[END]
            //Set Template Text[START]
            if (array_key_exists('template_text_1', $compData)) {
                if ($compData['template_text_1'] == '') {
                    $compData['template_text_1'] = -1;
                } else {
                    $compData['template_text_1'] = $compData['template_text_1'];
                }
            }
            if (array_key_exists('template_text_2', $compData)) {
                if ($compData['template_text_2'] == '') {
                    $compData['template_text_2'] = -1;
                } else {
                    $compData['template_text_2'] = $compData['template_text_2'];
                }
            }
            if (array_key_exists('template_text_3', $compData)) {
                if ($compData['template_text_3'] == '') {
                    $compData['template_text_3'] = -1;
                } else {
                    $compData['template_text_3'] = $compData['template_text_3'];
                }
            }
            if (array_key_exists('template_text_4', $compData)) {
                if ($compData['template_text_4'] == '') {
                    $compData['template_text_4'] = -1;
                } else {
                    $compData['template_text_4'] = $compData['template_text_4'];
                }
            }
            //Set Template Text[END]
            //Set Extra Comment
            if (array_key_exists('comp_extra_comment', $compData)) {
                $compData['comp_extra_comment'] = $compData['comp_extra_comment'];
            }
            if ($compData['status'] == 'Enabled') {
                $status = 1;
            } else {
                $status = 0;
            }
            $compData['status'] = $status;

            $productcompInstance->addData($compData);
            try {
                $productcompInstance->save();
                if (isset($compData['products']) && is_string($compData['products'])) {
                    $this->insertProductIds($productcompInstance->getId(), $productsIds);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
            }
            return $productcompInstance->save();
        }
    }

    /**
     * Check attribute option value already exist or not
     *
     * @param  [type] $optionsArr [description]
     * @param  [type] $attrCode   [description]
     * @param  [type] $attrLabel  [description]
     * @return [type]             [description]
     */
    private function checkAttrExist($optionsArr, $attrCode, $attrLabel)
    {
        $attributeInfo = $this->attributeFactory->getCollection()
            ->addFieldToFilter('attribute_code', ['eq' => $attrCode])
            ->getFirstItem();
        $option = [];
        if ($attributeInfo->getAttributeId()) {
            $option['attribute_id'] = $attributeInfo->getAttributeId();
            $option['value'][$attrLabel][0] = $attrLabel;
        }
        if (count($optionsArr) > 0) {
            $optValSave = [];
            foreach ($optionsArr as $key => $value) {
                $optValSave[] = trim($value['value']);
            }
            if (!in_array(trim($attrLabel), $optValSave)) {
                if (!empty($option)) {
                    $eavSetup = $this->eavSetupFactory->create();
                    $eavSetup->addAttributeOption($option);
                }
            }
        } else {
            if (!empty($option)) {
                $eavSetup = $this->eavSetupFactory->create();
                $eavSetup->addAttributeOption($option);
            }
        }

    }

    /**
     * Check url key is exist or not
     *
     * @param  [type] $entityId [description]
     * @param  [type] $urlKey   [description]
     * @return [type]           [description]
     */
    private function checkUrlKey($entityId, $urlKey)
    {
        $model = $this->productcompFactory->create()->getCollection()->addFieldToFilter("url_key", ["like" => $urlKey])->addFieldToFilter("entity_id", ["neq" => $entityId])->setOrder("productcompatibility_id", "DESC")->getFirstItem();
        if (!empty($model->getData())) {
            return 0;
        } else {
            return 1;
        }
    }
    /**
     * Insert selected product ids and position of associated products
     *
     * @param  [type] $compId [description]
     * @param  [type] $productsId [description]
     * @return [type]             [description]
     */
    private function insertProductIds($compId, $productsId)
    {
        $tempSitProductComp = $this->sitProductCompProduct;
        $collection = $tempSitProductComp->getCollection()->addFieldToFilter("productcompatibility_id", ["eq" => $compId]);
        foreach ($collection->getData() as $value) {
            $tempSitProductComp->load($value["rel_id"]);
            $tempSitProductComp->delete();
            $tempSitProductComp->unsetData();
        }
        try {
            foreach ($productsId as $key => $proId) {
                $fields = ["productcompatibility_id" => $compId, "product_id" => $proId];
                $this->insertProduct($fields);
            }
        } catch (\Magento\Framework\Validator\Exception $e) {
            $this->messageManager->addError('Product Id not Saved.');
        }
    }

    /**
     * Add product ids array in collection data
     *
     * @param  [type] $productArray [description]
     * @return [type]               [description]
     */
    private function insertProduct($productArray)
    {
        $save = $this->sitProductCompFactory->create();
        $save->setData($productArray);
        return $save->save();
    }
}
