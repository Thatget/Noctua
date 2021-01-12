<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace SIT\WebformApi\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\WebformApi\Model\Countryprice;
use SIT\WebformApi\Model\ResourceModel\Countryprice\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;
    protected $Countrypricemodel;

    public function __construct(Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Countryprice $Countrypricemodel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->Countrypricemodel = $Countrypricemodel;
        parent::__construct($context);
    }

    public function execute()
    {
        $jobData = $this->collectionFactory->create();

        foreach ($jobData as $value) {
            $templateId[] = $value['id'];
        }
        $parameterData = $this->getRequest()->getParams('id');
        $selectedAppsid = $this->getRequest()->getParams('id');
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
        $collection->addFieldToFilter('id', ['in' => $selectedAppsid]);
        $delete = 0;
        $model = [];
        foreach ($collection as $item) {
            $this->deleteById($item->getId());
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
        $item = $this->Countrypricemodel->load($id);
        $item->delete();
        return;
    }
}
