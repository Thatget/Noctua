<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [07-06-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\CPU;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory;

class MassDelete extends Action
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
        $parameterData = $this->getRequest()->getParams('delete');
        $selectedAppsid = $this->getRequest()->getParams('delete');
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
            $this->deleteComp($item->getEntityId());
            $count++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $count));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * [deleteComp description]
     * @param  [type] $entity_id [description]
     * @param  [type] $param     [description]
     * @return [type]            [description]
     */
    public function deleteComp($entity_id)
    {
        $item = $this->productcompFactory->create()->load($entity_id);
        $item->delete();
        return;
    }
}
