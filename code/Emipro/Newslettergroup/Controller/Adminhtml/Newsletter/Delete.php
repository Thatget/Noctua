<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Controller\Adminhtml\Newsletter;

use Emipro\Newslettergroup\Model\Newsletter;
use Emipro\Newslettergroup\Model\Usersubscriber;
use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var Newsletter
     */
    protected $newsLetterGroupModel;

    /**
     * @var Usersubscriber
     */
    protected $userSubscriberModel;

    /**
     * [__construct description]
     * @param Newsletter     $newsLetterGroupModel [description]
     * @param Usersubscriber $userSubscriberModel  [description]
     * @param Action\Context $context              [description]
     */
    public function __construct(
        Newsletter $newsLetterGroupModel,
        Usersubscriber $userSubscriberModel,
        Action\Context $context
    ) {
        $this->newsLetterGroupModel = $newsLetterGroupModel;
        $this->userSubscriberModel = $userSubscriberModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $collMod = $this->newsLetterGroupModel;
                $collMod->load($id);
                $collMod->delete();

                $collModl = $this->userSubscriberModel->getCollection()->addFieldToFilter('group_id', $id);
                foreach ($collModl as $item) {
                    $item->delete();
                }
                $this->messageManager->addSuccess(__('The Group has been deleted.'));
                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/index', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a Group to delete.'));
        return $resultRedirect->setPath('*/*/index');
    }
}
