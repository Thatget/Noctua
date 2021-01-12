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

class Delete extends Action
{
    /**
     * @var MainpagevideoFactory
     */
    protected $_mainpagevideoFactory;

    /**
     * [__construct description]
     * @param Context              $context       [description]
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
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id', null);

        try {
            $mainpagevideoData = $this->_mainpagevideoFactory->create()->load($id);
            if ($mainpagevideoData->getId()) {
                $mainpagevideoData->delete();
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
