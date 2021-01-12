<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Controller\Adminhtml\ProductVideo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductVideoNew\Model\Product as SitProductVideo;
use SIT\ProductVideoNew\Model\ProductFactory as SitProductVideoFactory;
use SIT\ProductVideoNew\Model\ProductVideoFactory;

class Save extends Action
{
    /**
     * @var ProductVideoFactory
     */
    protected $_productvideoFactory;

    /**
     * [__construct description]
     * @param Context              $context              [description]
     * @param ProductVideoFactory $productvideoFactory [description]
     */
    public function __construct(
        Context $context,
        ProductVideoFactory $productvideoFactory,
        SitProductVideo $sitProductVideo,
        SitProductVideoFactory $sitProductVideoFactory
    ) {
        $this->_productvideoFactory = $productvideoFactory;
        $this->sitProductVideo = $sitProductVideo;
        $this->sitProductVideoFactory = $sitProductVideoFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductVideoNew::productvideo');
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
            $params = [];
            $productvideonewData = $this->_productvideoFactory->create();
            $productvideonewData->setStoreId($storeId);
            $params['store'] = $storeId;
            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
            } else {
                $productvideonewData->load($data['entity_id']);
                $params['entity_id'] = $data['entity_id'];
            }
            $productvideonewData->addData($data);

            $this->_eventManager->dispatch(
                'sit_productvideonew_productvideo_prepare_save',
                ['object' => $this->_productvideoFactory, 'request' => $this->getRequest()]
            );

            try {
                $productvideonewData->save();
                if (isset($data['productvideonew_products'])
                    && is_string($data['productvideonew_products'])) {
                    $productsId = (array) json_decode($data['productvideonew_products']);
                    $this->insertProductIds($productvideonewData->getId(), $productsId);
                }
                $this->messageManager->addSuccessMessage(__('You saved this record.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $params['entity_id'] = $productvideonewData->getId();
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
    private function insertProductIds($videoId, $productsId)
    {
        $tempSitProductVideo = $this->sitProductVideo;
        $collection = $tempSitProductVideo->getCollection()->addFieldToFilter("productvideo_id", ["eq" => $videoId]);
        foreach ($collection->getData() as $value) {
            $tempSitProductVideo->load($value["rel_id"]);
            $tempSitProductVideo->delete();
            $tempSitProductVideo->unsetData();
        }
        try {
            /**
             * All fields value save from associated product and position null if position field will blank
             */
            foreach ($productsId as $key => $value) {
                if ($value != "" && $value >= 0) {
                    if (is_numeric($value)) {
                        $fields = ["productvideo_id" => $videoId, "product_id" => $key, "position" => $value];
                    } else {
                        $fields = ["productvideo_id" => $videoId, "product_id" => $key, "position" => $value];
                        $this->messageManager->addError('You have entered text value for product position.');
                    }
                } else {
                    $fields = ["productvideo_id" => $videoId, "product_id" => $key, "position" => null];
                }
                $this->insertProduct($fields);
            }
        } catch (\Magento\Framework\Validator\Exception $e) {
            $this->messageManager->addError('Product Id not Saved.');
        }
    }
    private function insertProduct($productArray)
    {
        $save = $this->sitProductVideoFactory->create();
        $save->setData($productArray);
        $save->save();
    }
}
