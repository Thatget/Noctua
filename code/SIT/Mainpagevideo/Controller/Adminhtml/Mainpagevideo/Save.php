<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Mainpagevideo\Controller\Adminhtml\Mainpagevideo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\Mainpagevideo\Model\MainpagevideoFactory;

class Save extends Action
{
    /**
     * @var MainpagevideoFactory
     */
    protected $_mainpagevideoFactory;

    /**
     * [__construct description]
     * @param Context              $context              [description]
     * @param MainpagevideoFactory $mainpagevideoFactory [description]
     */
    public function __construct(
        Context $context,
        MainpagevideoFactory $mainpagevideoFactory
    ) {
        $this->_mainpagevideoFactory = $mainpagevideoFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_Mainpagevideo::mainpagevideo');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $params = [];
            $mainpagevideoData = $this->_mainpagevideoFactory->create();
            $mainpagevideoData->setStoreId($storeId);
            $params['store'] = $storeId;
            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
            } else {
                $mainpagevideoData->load($data['entity_id']);
                $params['entity_id'] = $data['entity_id'];
            }
            $mainpagevideoData->addData($data);

            $this->_eventManager->dispatch(
                'sit_mainpagevideo_mainpagevideo_prepare_save',
                ['object' => $this->_mainpagevideoFactory, 'request' => $this->getRequest()]
            );

            try {
                $mainpagevideoData->save();
                $this->messageManager->addSuccessMessage(__('You saved this record.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $params['entity_id'] = $mainpagevideoData->getId();
                    $params['_current'] = true;
                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
            }

            $this->_getSession()->setFormData($this->getRequest()->getPostValue());
            return $resultRedirect->setPath('*/*/edit', $params);
        }
        return $resultRedirect->setPath('*/*/');
    }

}
