<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Coolers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory;

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
     * @var ProductCompatibilityFactory
     */
    protected $productcompFactory;

    /**
     * [__construct description]
     * @param Context                     $context            [description]
     * @param Filter                      $filter             [description]
     * @param ProductCompatibilityFactory $productcompFactory [description]
     * @param CollectionFactory           $collectionFactory  [description]
     */
    public function __construct(Context $context,
        Filter $filter,
        ProductCompatibilityFactory $productcompFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->productcompFactory = $productcompFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $productCompData = $this->collectionFactory->create();

        foreach ($productCompData as $value) {
            $productCompId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams('status');
        $selectedAppsid = $this->getRequest()->getParams('status');
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $productCompId;
            } else {
                $selectedAppsid = array_diff($productCompId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
        $count = 0;
        foreach ($collection as $item) {
            $this->setStatus($item->getEntityId(), $this->getRequest()->getParam('status'));
            $count++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $count));
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
        $item = $this->productcompFactory->create()->load($entity_id);
        $item->setStatus($param)->save();
        return;
    }
}
