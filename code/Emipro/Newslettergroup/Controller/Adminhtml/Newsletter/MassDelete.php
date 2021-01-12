<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Controller\Adminhtml\Newsletter;

use Emipro\Newslettergroup\Model\ResourceModel\Newsletter\CollectionFactory;
use Emipro\Newslettergroup\Model\Usersubscriber;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

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
     * @var Usersubscriber
     */
    protected $userSubscriber;

    /**
     * [__construct description]
     * @param Context                                   $context            [description]
     * @param Filter                                    $filter             [description]
     * @param \Magento\Framework\App\ResourceConnection $ResourceConnection [description]
     * @param CollectionFactory                         $collectionFactory  [description]
     * @param Usersubscriber                            $userSubscriber     [description]
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Magento\Framework\App\ResourceConnection $ResourceConnection,
        CollectionFactory $collectionFactory,
        Usersubscriber $userSubscriber
    ) {
        $this->filter = $filter;
        $this->ResourceConnection = $ResourceConnection;
        $this->collectionFactory = $collectionFactory;
        $this->userSubscriber = $userSubscriber;
        parent::__construct($context);
    }

    public function execute()
    {
        $templateData = $this->collectionFactory->create();

        foreach ($templateData as $value) {
            $templateId[] = $value['id'];
        }
        $parameterData = $this->getRequest()->getParams('id');
        $selectedUserid = $this->getRequest()->getParams('id');
        if (array_key_exists("selected", $parameterData)) {
            $selectedUserid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedUserid = $templateId;
            } else {
                $selectedUserid = array_diff($templateId, $parameterData['excluded']);
            }
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', array('in' => $selectedUserid));
        $delete = 0;
        $model = [];
        foreach ($collection as $item) {
            $item->delete();
            $collModl = $this->userSubscriber->getCollection()->addFieldToFilter('group_id', $item['id']);
            foreach ($collModl as $item) {
                $item->delete();
            }
            $delete++;
        }

        $this->messageManager->addSuccess(__('A total of %1 records have been deleted.', $delete));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
