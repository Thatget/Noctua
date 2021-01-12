<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\Adminhtml\ProductFaq;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductFaqNew\Model\Product as SitFaqProduct;
use SIT\ProductFaqNew\Model\ProductFactory as SitFaqProductFactory;
use SIT\ProductFaqNew\Model\ProductFaqFactory;

class Save extends Action
{
    /**
     * @var ProductFaqFactory
     */
    protected $_productfaqnewFactory;

    /**
     * [__construct description]
     * @param Context              $context              [description]
     * @param ProductFaqFactory    $productfaqnewFactory [description]
     * @param SitFaqProduct        $sitFaqProduct        [description]
     * @param SitFaqProductFactory $sitFaqProductFactory [description]
     */
    public function __construct(
        Context $context,
        ProductFaqFactory $productfaqnewFactory,
        SitFaqProduct $sitFaqProduct,
        SitFaqProductFactory $sitFaqProductFactory
    ) {
        $this->_productfaqnewFactory = $productfaqnewFactory;
        $this->sitFaqProduct = $sitFaqProduct;
        $this->sitFaqProductFactory = $sitFaqProductFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductFaqNew::productfaq');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if ($data["faq_multi_category"] != "") {
                $data["faq_multi_category"] = implode(",", $data["faq_multi_category"]);
            }
            /*generate url key*/

            $params = [];
            $productfaqnewData = $this->_productfaqnewFactory->create();
            $productfaqnewData->setStoreId($storeId);
            $params['store'] = $storeId;
            $urlKey = $data["faq_que"];
            $urlKey = str_replace(' ', '-', $urlKey); // Replaces all spaces with hyphens.
            $urlKey = preg_replace('/[^A-Za-z0-9\-]/', '', $urlKey); // Removes special chars.
            $urlKey = preg_replace('/-+/', '-', $urlKey); // Replaces multiple hyphens with single one.
            if (empty($data['entity_id'])) {
                $data["url_key"] = strtolower($urlKey);
                $data['entity_id'] = null;
                $checkUrl = $this->checkUrlKeySave($data["url_key"]);
                if ($checkUrl) {
                    $data["url_key"] = strtolower($checkUrl);
                }
            } else {
                $productfaqnewData->load($data['entity_id']);
                $params['entity_id'] = $data['entity_id'];
                $checkUrl = $this->checkUrlKeyEdit($data['entity_id'], $urlKey);
                if ($checkUrl) {
                    $data["url_key"] = strtolower($urlKey);
                }
            }
            /*Changed by MD for category tree[START][19-09-2019]*/
            if (!empty($data['category_id'])) {
                $data['category_id'] = implode(',', $data['category_id']);
            }
            /*Changed by MD for category tree[END][19-09-2019]*/
            $productfaqnewData->addData($data);
            if (!empty($data['entity_id'])) {
                $productfaqnewData->setData('faq_multi_category', $data["faq_multi_category"]);
                $productfaqnewData->getResource()->saveAttribute($productfaqnewData, 'faq_multi_category');
            }

            $this->_eventManager->dispatch(
                'sit_productfaqnew_productfaqnew_prepare_save',
                ['object' => $this->_productfaqnewFactory, 'request' => $this->getRequest()]
            );

            try {
                $productfaqnewData->save();

                if (isset($data['productfaqnew_products'])
                    && is_string($data['productfaqnew_products'])) {
                    $productsId = (array) json_decode($data['productfaqnew_products']);
                    $this->insertProductIds($productfaqnewData->getId(), $productsId);
                }
                $this->messageManager->addSuccessMessage(__('You saved this record.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $params['entity_id'] = $productfaqnewData->getId();
                    $params['_current'] = true;
                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
            }

            $this->_getSession()->setFormData($this->getRequest()->getPostValue());
            return $resultRedirect->setPath('*/*/edit', $params);
        }
        return $resultRedirect->setPath('*/*/');
    }
    private function insertProductIds($questionId, $productsId)
    {
        $tempSitFaqProduct = $this->sitFaqProduct;
        $collection = $tempSitFaqProduct->getCollection()->addFieldToFilter("productfaq_id", ["eq" => $questionId]);
        foreach ($collection->getData() as $value) {
            $tempSitFaqProduct->load($value["rel_id"]);
            $tempSitFaqProduct->delete();
            $tempSitFaqProduct->unsetData();
        }
        try {
            /**
             * All fields value save from associated product and position null if position field will blank
             */
            foreach ($productsId as $key => $value) {
                if ($value != "" && $value >= 0) {
                    if (is_numeric($value)) {
                        $fields = ["productfaq_id" => $questionId, "product_id" => $key, "position" => $value];
                    } else {
                        $fields = ["productfaq_id" => $questionId, "product_id" => $key, "position" => $value];
                        $this->messageManager->addWarning('You have entered text value for product position. By default set value 0');
                    }
                } else {
                    $fields = ["productfaq_id" => $questionId, "product_id" => $key, "position" => null];
                }
                $this->insertProduct($fields);
            }
        } catch (\Magento\Framework\Validator\Exception $e) {
            $this->messageManager->addError('Product Id not Saved.');
        }
    }
    private function insertProduct($productArray)
    {
        $save = $this->sitFaqProductFactory->create();
        $save->setData($productArray);
        $save->save();
    }
    private function checkUrlKeySave($urlKey = null)
    {
        $storeId = (int) $this->getRequest()->getParam('store_id');
        $model = $this->_productfaqnewFactory->create()->getCollection()->addFieldToSelect("*")->setStoreId($storeId)->addFieldToFilter("url_key", ["like" => $urlKey])->setOrder("productfaq_id", "DEC")->getFirstItem();
        /**
         * Return url or flag key for save
         */
        if (!empty($model->getData())) {
            return $urlKey = preg_match('/(.*)-(\d+)$/', $model->getUrlKey(), $matches)
            ? $matches[1] . '-' . ($matches[2] + 1)
            : $urlKey . '-1';
        } else {
            return false;
        }
    }
    private function checkUrlKeyEdit($entityId, $urlKey)
    {
        /**
         * Return url or flag key for edit
         */
        $storeId = (int) $this->getRequest()->getParam('store_id');
        $model = $this->_productfaqnewFactory->create()->getCollection()->setStoreId($storeId)->addFieldToFilter("url_key", ["like" => $urlKey])->addFieldToFilter("entity_id", ["neq" => $entityId])->setOrder("productfaq_id", "DESC")->getFirstItem();
        if (!empty($model->getData())) {
            return 0;
        } else {
            return 1;
        }
    }
}
