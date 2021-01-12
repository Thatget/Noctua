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
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductVideoNew\Model\ProductVideo;
use SIT\ProductVideoNew\Model\ResourceModel\ProductVideo\CollectionFactory;

class MassLanguage extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductVideoNew
     */
    protected $productVideo;

    /**
     * [__construct description]
     * @param Context           $context           [description]
     * @param Filter            $filter            [description]
     * @param CollectionFactory $collectionFactory [description]
     * @param ProductVideoNew     $productVideo        [description]
     */
    public function __construct(Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductVideo $productVideo
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productVideo = $productVideo;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $videoData = $this->collectionFactory->create();

        foreach ($videoData as $value) {
            $templateId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams('language');
        $selectedAppsid = $this->getRequest()->getParams('language');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $templateId;
            } else {
                $selectedAppsid = array_diff($templateId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
        $language = 0;
        foreach ($collection as $item) {
            $this->setVideoLanguage($item->getEntityId(), $this->getRequest()->getParam('language'));
            $language++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $language));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * setVideoLanguage of selected questions
     * @param [type] $id    [description]
     * @param [type] $param [description]
     */
    public function setVideoLanguage($entity_id, $param)
    {
        $videoModel = $this->productVideo->load($entity_id);
        $videoModel->setVideoLanguage($param)->save();
        return;
    }
}
