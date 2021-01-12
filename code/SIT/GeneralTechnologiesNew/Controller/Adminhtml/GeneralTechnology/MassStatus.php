<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SIT\GeneralTechnologiesNew\Controller\Adminhtml\GeneralTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\GeneralTechnologiesNew\Model\GeneralTechnology;
use SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory;

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
     * @var GeneralTechnology
     */
    protected $generalTechnologymodel;

    /**
     * [__construct description]
     * @param Context           $context           [description]
     * @param Filter            $filter            [description]
     * @param CollectionFactory $collectionFactory [description]
     * @param GeneralTechnology       $generalTechnologymodel  [description]
     */
    public function __construct(Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        GeneralTechnology $generalTechnologymodel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->generalTechnologymodel = $generalTechnologymodel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $generalTechnologyData = $this->collectionFactory->create();

        foreach ($generalTechnologyData as $value) {
            $generalTechnologyId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams('status');
        $selectedAppsid = $this->getRequest()->getParams('status');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $generalTechnologyId;
            } else {
                $selectedAppsid = array_diff($generalTechnologyId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
        $status = 0;
        foreach ($collection as $item) {
            $this->setStatus($item->getEntityId(), $this->getRequest()->getParam('status'));
            $status++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $status));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * [setStatus description]
     * @param [type] $id    [description]
     * @param [type] $Param [description]
     */
    public function setStatus($entity_id, $param)
    {
        $item = $this->generalTechnologymodel->load($entity_id);
        $item->setStatus($param)->save();
        return;
    }
}
