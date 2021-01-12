<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductVideoNew\Controller\Adminhtml\ProductVideo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductVideoNew\Model\ProductVideoFactory;

class Delete extends Action
{
    /**
     * @var ProductVideoFactory
     */
    protected $_productvideoFactory;

    /**
     * [__construct description]
     * @param Context              $context       [description]
     * @param ProductVideoFactory $productvideoFactory [description]
     */
    public function __construct(
        Context $context,
        ProductVideoFactory $productvideoFactory
    ) {
        $this->_productvideoFactory = $productvideoFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductVideoNew::productvideo');
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
            $productvideoData = $this->_productvideoFactory->create()->load($id);
            if ($productvideoData->getId()) {
                $productvideoData->delete();
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
