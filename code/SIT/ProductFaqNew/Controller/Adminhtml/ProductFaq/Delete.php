<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\Adminhtml\ProductFaq;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductFaqNew\Model\ProductFaqFactory;

class Delete extends Action
{
    /**
     * @var ProductFaqFactory
     */
    protected $_productfaqnewFactory;

    /**
     * [__construct description]
     * @param Context              $context       [description]
     * @param ProductFaqFactory $productfaqnewFactory [description]
     */
    public function __construct(
        Context $context,
        ProductFaqFactory $productfaqnewFactory
    ) {
        $this->_productfaqnewFactory = $productfaqnewFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductFaqNew::productfaq');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id', null);

        try {
            $productfaqnewData = $this->_productfaqnewFactory->create()->load($id);
            if ($productfaqnewData->getId()) {
                $productfaqnewData->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the record.'));
            } else {
                $this->messageManager->addErrorMessage(__('Record does not exist.'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*');
    }
}
