<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-05-2019]
 */

namespace SIT\ProductCompatibility\Controller\Adminhtml\MassAction;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\Product as SitProductComp;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;
use SIT\ProductCompatibility\Model\ProductFactory as SitProductCompFactory;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory;

class MassActions extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductCompatibilityFactory
     */
    protected $productcompFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * @var SitProductComp
     */
    protected $sitProductCompProduct;

    /**
     * @var SitProductCompFactory
     */
    protected $sitProductCompFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * @var \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \SIT\ProductCompatibility\Model\ProductFactory
     */
    protected $compProductFactory;

    /**
     * [__construct description]
     * @param Context                                                     $context               [description]
     * @param \Magento\Framework\Registry                                 $registry              [description]
     * @param Registry                                                    $coreRegistry          [description]
     * @param ProductCompatibilityFactory                                 $productcompFactory    [description]
     * @param CollectionFactory                                           $collectionFactory     [description]
     * @param \Magento\Framework\Session\SessionManagerInterface          $coreSession           [description]
     * @param SitProductComp                                              $sitProductCompProduct [description]
     * @param SitProductCompFactory                                       $sitProductCompFactory [description]
     * @param RequestInterface                                            $request               [description]
     * @param ProductCompHelper                                           $prodCompHelper        [description]
     * @param \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory      [description]
     * @param \Magento\Eav\Setup\EavSetupFactory                          $eavSetupFactory       [description]
     * @param \SIT\ProductCompatibility\Model\ProductFactory              $compProductFactory    [description]
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        Registry $coreRegistry,
        ProductCompatibilityFactory $productcompFactory,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        SitProductComp $sitProductCompProduct,
        SitProductCompFactory $sitProductCompFactory,
        RequestInterface $request,
        ProductCompHelper $prodCompHelper,
        \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \SIT\ProductCompatibility\Model\ProductFactory $compProductFactory
    ) {
        $this->registry = $registry;
        $this->coreRegistry = $coreRegistry;
        $this->productcompFactory = $productcompFactory;
        $this->collectionFactory = $collectionFactory;
        $this->coreSession = $coreSession;
        $this->sitProductCompProduct = $sitProductCompProduct;
        $this->sitProductCompFactory = $sitProductCompFactory;
        $this->request = $request;
        $this->prodCompHelper = $prodCompHelper;
        $this->attributeFactory = $attributeFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->compProductFactory = $compProductFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $storeId = $this->coreSession->getProCompStore();
        $collection->setStoreId($storeId);
        $ids = $this->registry->registry('entity_id');
        if ($ids == '') {
            $ids = $this->coreSession->getProCompId();
        }
        $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        //Get Compatibility's value
        $massAction = $this->getRequest()->getParam('mass_actions');
        $status = $this->getRequest()->getParam('status');
        $socket = $this->getRequest()->getParam('comp_socket');
        $manu = $this->getRequest()->getParam('comp_manufacture');
        $model = $this->getRequest()->getParam('comp_model');
        $compValue = $this->getRequest()->getParam('comp_value');
        $type = $this->getRequest()->getParam('comp_type');
        $products = $this->getRequest()->getParam('assign_products');
        $changeTemp = $this->getRequest()->getParam('change_temp');
        $template_1 = $this->getRequest()->getParam('template_text_1');
        $template_2 = $this->getRequest()->getParam('template_text_2');
        $template_3 = $this->getRequest()->getParam('template_text_3');
        $template_4 = $this->getRequest()->getParam('template_text_4');
        $duplicate = $this->getRequest()->getParam('duplicate');
        $howMany = $this->getRequest()->getParam('how_many');
        $unused = $this->getRequest()->getParam('unused');

        // Adam: Fix issue: "Change Series" functionality CPU compatibility list
        $compSeries = $this->getRequest()->getParam('comp_series');

        if ($unused == 'unusedcomp') {
            $compType = $this->coreSession->getProCompType();
        } else {
            $compType = $this->getRequest()->getParam('comptype');
        }
        $count = 0;
        foreach ($collection as $item) {
            if ($socket != '' && $massAction == 'change_socket') {
                $this->setSocketData($item->getEntityId(), $socket);
            }
            if ($manu != '' && $massAction == 'change_manu') {
                $this->setManufacturerData($item->getEntityId(), $manu);
            }
            if ($model != '' && $massAction == 'change_model') {
                $this->setModelData($item->getEntityId(), $model);
            }

            // Adam: Fix issue: "Change Series" functionality CPU compatibility list
            if ($compSeries != '' && $massAction == 'change_series') {
                $this->setSeriesData($item->getEntityId(), $compSeries);
            }

            if ($compValue != '' && $massAction == 'change_comp') {
                $this->setCompatibilityData($item->getEntityId(), $compValue);
            }
            if ($type != '' && $massAction == 'change_type') {
                $this->setTypeData($item->getEntityId(), $type);
            }
            if ($products != '' && $massAction == 'assign_products') {
                $this->setProductData($item->getEntityId(), $products);
            }
            if ($massAction == 'change_temp_text') {
                $this->setTemplateText1($item->getEntityId(), $template_1, $storeId);
                $this->setTemplateText2($item->getEntityId(), $template_2, $storeId);
                $this->setTemplateText3($item->getEntityId(), $template_3, $storeId);
                $this->setTemplateText4($item->getEntityId(), $template_4, $storeId);
            }
            if ($massAction == 'multi_action' && $changeTemp == 1) {
                $this->setTemplateText1($item->getEntityId(), $template_1, $storeId);
                $this->setTemplateText2($item->getEntityId(), $template_2, $storeId);
                $this->setTemplateText3($item->getEntityId(), $template_3, $storeId);
                $this->setTemplateText4($item->getEntityId(), $template_4, $storeId);
            }
            if ($massAction == 'multi_action') {
                $this->setStatus($item->getEntityId(), $status);
                if ($products != '') {
                    $this->setProductData($item->getEntityId(), $products);
                }
                if ($manu != '') {
                    $this->setManufacturerData($item->getEntityId(), $manu);
                }
                if ($model != '') {
                    $this->setModelData($item->getEntityId(), $model);
                }
                if ($compValue != '') {
                    $this->setCompatibilityData($item->getEntityId(), $compValue);
                }
            }
            if ($massAction == 'duplicate') {
                $this->duplicateComp($item->getEntityId(), $duplicate, $howMany);
            }
            $count++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $count));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($unused == 'unusedcomp') {
            return $resultRedirect->setPath('sit_productcompatibility/unused/massActions/unused/unusedcomp');
        } else {
            if ($compType == 'Case') {
                $caseType = $this->getRequest()->getParam('casetype');
                return $resultRedirect->setPath('*/' . $caseType . '/massActions/comptype/' . $compType);
            } else {
                return $resultRedirect->setPath('*/' . $compType . '/massActions/comptype/' . $compType);
            }
        }
    }


    /**
     * Adam: Fix issue: "Change Series" functionality CPU compatibility list
     * 
     * [setSeriesData description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     */
    public function setSeriesData($entity_id, $param)
    {
        $seriesInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SERIES);
        $seriestId = $seriesInfo->getAttributeId();
        $seriesAllOption = $this->prodCompHelper->getAttributeOptionAll($seriestId);
        $seriesAttrCheck = $this->checkAttrExist($seriesAllOption, ProductCompHelper::COMP_SERIES, trim($param));
        $series_after_save = $this->prodCompHelper->getAttributeOptionAll($seriestId);
        $data[ProductCompHelper::COMP_SERIES] = $this->prodCompHelper->getAttrOptionId($series_after_save, trim($param));
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->addData($data)->save();
        return;
    }

    /**
     * [setSocketData description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     */
    public function setSocketData($entity_id, $param)
    {
        $socketInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
        $socketId = $socketInfo->getAttributeId();
        $socketAllOption = $this->prodCompHelper->getAttributeOptionAll($socketId);
        $socketAttrCheck = $this->checkAttrExist($socketAllOption, ProductCompHelper::COMP_SOCKET, trim($param));
        $socket_after_save = $this->prodCompHelper->getAttributeOptionAll($socketId);
        $data['comp_socket'] = $this->prodCompHelper->getAttrOptionId($socket_after_save, trim($param));
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->addData($data)->save();
        return;
    }

    /**
     * [setManufacturerData description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     */
    public function setManufacturerData($entity_id, $param)
    {
        $manufInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
        $manufId = $manufInfo->getAttributeId();
        $manufAllOption = $this->prodCompHelper->getAttributeOptionAll($manufId);
        $manufAttrCheck = $this->checkAttrExist($manufAllOption, ProductCompHelper::COMP_MANUFACTURE, trim($param));
        $manuf_after_save = $this->prodCompHelper->getAttributeOptionAll($manufId);
        $data['comp_manufacture'] = $this->prodCompHelper->getAttrOptionId($manuf_after_save, trim($param));
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->addData($data)->save();
        return;
    }

    /**
     * [setModelData description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     */
    public function setModelData($entity_id, $param)
    {
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        $modelAttrCheck = $this->checkAttrExist($modelAllOption, ProductCompHelper::COMP_MODEL, trim($param));
        $model_after_save = $this->prodCompHelper->getAttributeOptionAll($modelId);
        $data['comp_model'] = $this->prodCompHelper->getAttrOptionId($model_after_save, trim($param));
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->addData($data)->save();
        return;
    }

    /**
     * [setCompatibilityData description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     */
    public function setCompatibilityData($entity_id, $param)
    {
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->setCompValue($param)->save();
        return;
    }

    /**
     * [setTypeData description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     */
    public function setTypeData($entity_id, $param)
    {
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->setCompType($param)->save();
        return;
    }

    /**
     * [setProductData description]
     * @param [type] $id    [description]
     * @param [type] $Param [description]
     */
    public function setProductData($entityId, $productIds)
    {
        $tempSitProductComp = $this->sitProductCompProduct;
        $collection = $tempSitProductComp->getCollection()->addFieldToFilter("productcompatibility_id", ["eq" => $entityId]);
        foreach ($collection->getData() as $value) {
            $tempSitProductComp->load($value["rel_id"]);
            $tempSitProductComp->delete();
            $tempSitProductComp->unsetData();
        }
        try {
            foreach ($productIds as $key => $value) {
                if ($value != "" && $value >= 0) {
                    $fields = ["productcompatibility_id" => $entityId, "product_id" => $value];
                    $this->insertProduct($fields);
                }
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
        $save->save();
        return;
    }

    /**
     * [setTemplateText1 description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     * @param [type] $storeId   [description]
     */
    public function setTemplateText1($entity_id, $param, $storeId)
    {
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->setStoreId($storeId);
        $productcompInstance->setTemplateText1($param)->save();
        return;
    }

    /**
     * [setTemplateText2 description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     * @param [type] $storeId   [description]
     */
    public function setTemplateText2($entity_id, $param, $storeId)
    {
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->setStoreId($storeId);
        $productcompInstance->setTemplateText2($param)->save();
        return;
    }

    /**
     * [setTemplateText3 description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     * @param [type] $storeId   [description]
     */
    public function setTemplateText3($entity_id, $param, $storeId)
    {
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->setStoreId($storeId);
        $productcompInstance->setTemplateText3($param)->save();
        return;
    }

    /**
     * [setTemplateText4 description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     * @param [type] $storeId   [description]
     */
    public function setTemplateText4($entity_id, $param, $storeId)
    {
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->setStoreId($storeId);
        $productcompInstance->setTemplateText4($param)->save();
        return;
    }

    /**
     * [setStatus description]
     * @param [type] $entity_id [description]
     * @param [type] $param     [description]
     */
    public function setStatus($entity_id, $param)
    {
        $productcompInstance = $this->productcompFactory->create()->load($entity_id);
        $productcompInstance->setStatus($param)->save();
        return;
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
     * [duplicateComp description]
     * @param  [type] $entity_id   [description]
     * @param  [type] $withProduct [description]
     * @param  [type] $howMany     [description]
     * @return [type]              [description]
     */
    public function duplicateComp($entity_id, $withProduct, $howMany)
    {
        $model = $this->productcompFactory->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $entity_id])->getFirstItem();
        $newUrlKey = $this->checkUrlKey($model->getUrlKey());
        $products = $this->compProductFactory->create()->getCollection()->addFieldToFilter("productcompatibility_id", ["eq" => $model->getEntityId()]);
        $duplicateData = $model->getData();
        $duplicateData["url_key"] = $newUrlKey;
        array_shift($duplicateData);
        if ($howMany != '') {
            for ($i = 1; $i <= $howMany; $i++) {
                if ($withProduct != 0) {
                    $this->createDuplicate($duplicateData, $products->getData());
                } else {
                    $this->duplicateWithoutProduct($duplicateData);
                }
            }
        } else {
            $this->createDuplicate($duplicateData, $products->getData());
        }
    }

    /**
     * Create duplicate from main table
     *
     * @param  [type] $data     [description]
     * @param  [type] $products [description]
     * @return [type]           [description]
     */
    private function createDuplicate($data, $products)
    {
        $model = $this->productcompFactory->create();
        $model->setData($data);
        $newQuestionId = $model->save();
        $this->insertProductIds($model->getEntityId(), $products);
    }

    /**
     * Create duplicate without product
     * @param  [type] $data     [description]
     */
    private function duplicateWithoutProduct($data)
    {
        $model = $this->productcompFactory->create();
        $model->setData($data);
        $newQuestionId = $model->save();
    }

    /**
     * generate url key
     *
     * @param  [string] $urlKey [description]
     * @return  string        [description]
     */
    private function checkUrlKey($urlKey)
    {
        $model = $this->productcompFactory->create()->getCollection()->addFieldToFilter("url_key", ["like" => "%" . $urlKey . "%"])->setOrder("productcompatibility_id", "DESC")->getFirstItem();
        $urlKey = $model->getUrlKey();

        return $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
            ? $matches[1] . '-' . ($matches[2] + 1)
            : $urlKey . '-1';
    }

    /**
     * Loop of product ids
     *
     * @param  [type] $questionId [description]
     * @param  [type] $products   [description]
     * @return [type]             [description]
     */
    private function insertProductIds($questionId, $products)
    {
        try {
            foreach ($products as $key => $value) {
                $fields = ["productcompatibility_id" => $questionId, "product_id" => $value["product_id"], "position" => $value["position"]];
                $this->insertProduct($fields);
            }
        } catch (\Magento\Framework\Validator\Exception $e) {
            $this->messageManager->addError('Product Id not Saved.');
        }
    }
}
