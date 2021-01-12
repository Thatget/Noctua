<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\GeneralNews\Controller\Adminhtml\News;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\GeneralNews\Model\News;
use SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory;

class MassStatus extends Action
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
     * @var News
     */
    protected $news;

    /**
     * [__construct description]
     * @param Context           $context           [description]
     * @param Filter            $filter            [description]
     * @param CollectionFactory $collectionFactory [description]
     * @param News     $news            [description]
     */
    public function __construct(Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        News $news
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->news = $news;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $newsData = $this->collectionFactory->create();
        $newsId = [];
        foreach ($newsData as $value) {
            $newsId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams('status');
        $selectedAppsid = $this->getRequest()->getParams('status');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $newsId;
            } else {
                $selectedAppsid = array_diff($newsId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
        $status = 0;
        $model = [];
        foreach ($collection as $item) {
            $this->setStatus($item->getEntityId(), $this->getRequest()->getParam('status'));
            $status++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $status));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * setStatus of selected questions
     * @param [type] $id    [description]
     * @param [type] $Param [description]
     */
    public function setStatus($entity_id, $Param)
    {
        $item = $this->news->load($entity_id);
        $item->setStatus($Param)->save();
        return;
    }
}
