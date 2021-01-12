<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Onibi\StoreLocator\Model\ResourceModel\Store\CollectionFactory;
use Onibi\StoreLocator\Model\Store;

class MassDelete extends \Magento\Backend\App\Action
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
     * @var Store
     */
    protected $storeModel;

    /**
     * [__construct description]
     * @param Context           $context           [description]
     * @param Filter            $filter            [description]
     * @param CollectionFactory $collectionFactory [description]
     * @param Store             $storeModel        [description]
     */
    public function __construct(Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Store $storeModel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->storeModel = $storeModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $jobData = $this->collectionFactory->create();

        foreach ($jobData as $value) {
            $templateId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams('entity_id');
        $selectedAppsid = $this->getRequest()->getParams('entity_id');
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
        $delete = 0;
        $model = [];
        foreach ($collection as $item) {
            $this->deleteById($item->getEntityId());
            $delete++;
        }
        $this->messageManager->addSuccess(__('A total of %1 Records have been deleted.', $delete));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * [deleteById description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteById($id)
    {
        $item = $this->storeModel->load($id);
        $item->delete();
        return;
    }
}
