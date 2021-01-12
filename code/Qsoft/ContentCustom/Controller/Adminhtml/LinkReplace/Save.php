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
use Magento\Backend\Model\Session;
use Qsoft\ContentCustom\Model\LinkReplaceFactory;

class Save extends \Magento\Backend\App\Action {
	/**
	 * @var Store
	 */
	protected $storeLocatorModel;

	/**
	 * @var Session
	 */
	protected $adminsession;

	/**
	 * [__construct description]
	 * @param Action\Context $context           [description]
	 * @param Store          $storeLocatorModel [description]
	 * @param Session        $adminsession      [description]
	 */
	public function __construct(
		Action\Context $context,
        LinkReplaceFactory $storeLocatorModel,
		Session $adminsession
	) {
		parent::__construct($context);
		$this->storeLocatorModel = $storeLocatorModel;
		$this->adminsession = $adminsession;
	}

	public function execute() {
		$data = $this->getRequest()->getPostValue();
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			$link_id = $this->getRequest()->getParam('link_id');
            $model = $this->storeLocatorModel->create();
			if ($link_id) {
                $model->load($link_id);
			}
            $model->setId($model->getId());
            $model->addData($data);
			try {
                $model->save();
				$this->messageManager->addSuccess(__('The data has been saved.'));
				$this->adminsession->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					if ($this->getRequest()->getParam('back') == 'add') {
						return $resultRedirect->setPath('*/*/add');
					} else {
						return $resultRedirect->setPath(
							'*/*/edit',
							[
								'link_id' => $this->storeLocatorModel->getLinkId(),
								'_current' => true,
							]
						);
					}
				}
				return $resultRedirect->setPath('*/*/');
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the data.'));
			}

			$this->_getSession()->setFormData($data);
			return $resultRedirect->setPath('*/*/edit', ['link_id' => $model->getId()]);
		}
		return $resultRedirect->setPath('*/*/');
	}

}
