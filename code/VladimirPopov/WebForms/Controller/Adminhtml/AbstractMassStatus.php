<?php
/**
 * @author      Vladimir Popov
 * @copyright   Copyright © 2019 Vladimir Popov. All rights reserved.
 */

namespace VladimirPopov\WebForms\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 */
class AbstractMassStatus extends \Magento\Backend\App\Action
{
    const ID_FIELD = 'id';

    const REDIRECT_URL = '*/*/';

    protected $status = 0;

    protected $redirect_params = [];

    protected $webformsHelper;

    protected $entityModel;

    protected $pageFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Model\AbstractModel $entityModel,
        \VladimirPopov\WebForms\Helper\Data $webformsHelper,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->webformsHelper = $webformsHelper;
        $this->entityModel = $entityModel;
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        if ($this->getRequest()->getParam('webform_id')) {
            return $this->webformsHelper->isAllowed($this->getRequest()->getParam('webform_id'));
        }
        if ($this->getRequest()->getParam('id')) {
            return $this->webformsHelper->isAllowed($this->getRequest()->getParam('id'));
        }
        return $this->_authorization->isAllowed('VladimirPopov_WebForms::manage_forms');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $Ids = $this->getRequest()->getParam(static::ID_FIELD);
        if (!is_array($Ids) || empty($Ids)) {
            $this->messageManager->addErrorMessage(__('Please select item(s).'));
        } else {
            try {
                foreach ($Ids as $id) {
                    $item = $this->entityModel->load($id);
                    $item->setIsActive($this->status)->save();
                    /*Changed by MD for set CMS Page Status[START][02-07-2019]*/
                    $pageCollection = $this->pageFactory->create()->getCollection()->addFieldToSelect(['page_id', 'content'])->addFieldToFilter('content', ['like' => '%webform_id=' . '"' . $id . '"' . '%'])->getData();
                    if ($pageCollection) {
                        $pageId = $pageCollection[0]['page_id'];
                        $collection = $this->pageFactory->create()->load($pageId);
                        if ($collection) {
                            $collection->setIsActive($this->status);
                            $collection->save();
                        }
                    }
                    /*Changed by MD for set CMS Page Status[END][02-07-2019]*/
                }
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been updated.', count($Ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL, $this->redirect_params);
    }
}
