<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Qsoft\ContentCustom\Controller\Adminhtml\LinkReplace;

use Magento\Backend\App\Action;

class Delete extends Action
{
    /**
     * @var Qsoft\ContentCustom\Model\Store
     */
    protected $model;

    /**
     * [__construct description]
     * @param Action\Context                  $context [description]
     * @param \Qsoft\ContentCustom\Model\Store $model   [description]
     */
    public function __construct(
        Action\Context $context,
        \Qsoft\ContentCustom\Model\LinkReplaceFactory $model
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
        $ids = $this->getRequest()->getParam('link_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = 0;
        if ($ids) {
            try {
                if(!is_array($ids))
                {
                    $ids = [$ids];
                }
                foreach ($ids as $id)
                {
                    $model = $this->modelStore->create();
                    $model->load($id);
                    $model->delete();
                }
                $this->messageManager->addSuccess(__('Record(s) deleted'));
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
