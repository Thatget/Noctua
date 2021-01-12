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

class Delete extends Action
{
    /**
     * @var TemplateTextFactory
     */
    protected $_templateTextFactory;

    /**
     * [__construct description]
     * @param Context              $context       [description]
     * @param TemplateTextFactory $templateTextFactory [description]
     */
    public function __construct(
        Context $context,
        TemplateTextFactory $templateTextFactory
    ) {
        $this->_templateTextFactory = $templateTextFactory;
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
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id', null);

        try {
            $templatetextData = $this->_templateTextFactory->create()->load($id);
            if ($templatetextData->getId()) {
                $templatetextData->delete();
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
