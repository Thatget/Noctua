<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SIT\ProductTechNew\Controller\Adminhtml\ProductTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductTechNew\Model\ProductTechnology;
use SIT\ProductTechNew\Model\ResourceModel\ProductTechnology\CollectionFactory;

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
     * @var ProductTechnology
     */
    protected $productTechnologymodel;

    /**
     * [__construct description]
     * @param Context           $context           [description]
     * @param Filter            $filter            [description]
     * @param CollectionFactory $collectionFactory [description]
     * @param ProductTechnology       $productTechnologymodel  [description]
     */
    public function __construct(Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductTechnology $productTechnologymodel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productTechnologymodel = $productTechnologymodel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $productTechnologyData = $this->collectionFactory->create();

        foreach ($productTechnologyData as $value) {
            $productTechnologyId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams('status');
        $selectedAppsid = $this->getRequest()->getParams('status');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $productTechnologyId;
            } else {
                $selectedAppsid = array_diff($productTechnologyId, $parameterData['excluded']);
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
        $item = $this->productTechnologymodel->load($entity_id);
        $item->setStatus($param)->save();
        return;
    }
}
