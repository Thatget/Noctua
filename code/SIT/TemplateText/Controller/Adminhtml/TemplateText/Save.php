<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\TemplateText\Controller\Adminhtml\TemplateText;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\TemplateText\Model\TemplateTextFactory;

class Save extends Action
{
    /**
     * @var TemplateTextFactory
     */
    protected $_templatetextFactory;

    /**
     * [__construct description]
     * @param Context              $context              [description]
     * @param TemplateTextFactory $templatetextFactory [description]
     */
    public function __construct(
        Context $context,
        TemplateTextFactory $templatetextFactory
    ) {
        $this->_templatetextFactory = $templatetextFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_TemplateText::templatetext');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $params = [];
            $templatetextData = $this->_templatetextFactory->create();
            $templatetextData->setStoreId($storeId);
            $params['store'] = $storeId;
            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
            } else {
                $templatetextData->load($data['entity_id']);
                $params['entity_id'] = $data['entity_id'];
            }
            $templatetextData->addData($data);

            $this->_eventManager->dispatch(
                'sit_templatetext_templatetext_prepare_save',
                ['object' => $this->_templatetextFactory, 'request' => $this->getRequest()]
            );

            try {
                $templatetextData->save();
                $this->messageManager->addSuccessMessage(__('You saved this record.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $params['entity_id'] = $templatetextData->getId();
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
