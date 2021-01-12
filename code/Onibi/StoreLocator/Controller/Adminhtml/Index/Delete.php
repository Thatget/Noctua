<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Delete extends Action
{
    /**
     * @var Onibi\StoreLocator\Model\Store
     */
    protected $model;

    /**
     * [__construct description]
     * @param Action\Context                  $context [description]
     * @param \Onibi\StoreLocator\Model\Store $model   [description]
     */
    public function __construct(
        Action\Context $context,
        \Onibi\StoreLocator\Model\Store $model
    ) {
        parent::__construct($context);
        $this->modelStore = $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Onibi_StoreLocator::index_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->modelStore;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Record deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Record does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
