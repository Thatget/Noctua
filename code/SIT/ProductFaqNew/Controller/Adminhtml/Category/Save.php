<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use SIT\ProductFaqNew\Model\Category;

class Save extends \Magento\Backend\App\Action {

	/**
	 * @var Category
	 */
	protected $categoryFaqObj;

	/**
	 * @var Session
	 */
	protected $adminsession;

	/**
	 * @param Action\Context $context        [description]
	 * @param Category       $categoryFaqObj [description]
	 * @param Session        $adminsession   [description]
	 */
	public function __construct(
		Action\Context $context,
		Category $categoryFaqObj,
		Session $adminsession
	) {
		parent::__construct($context);
		$this->categoryFaqObj = $categoryFaqObj;
		$this->adminsession = $adminsession;
	}

	public function execute() {
		$data = $this->getRequest()->getPostValue();
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			$id = $this->getRequest()->getParam('id');

			if ($id) {
				$this->categoryFaqObj->load($id);
			}

			$this->categoryFaqObj->setData($data);

			try {
				$this->categoryFaqObj->save();
				$this->messageManager->addSuccess(__('The data has been saved.'));
				$this->adminsession->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					if ($this->getRequest()->getParam('back') == 'add') {
						return $resultRedirect->setPath('*/*/add');
					} else {
						return $resultRedirect->setPath(
							'*/*/edit',
							[
								'id' => $this->categoryFaqObj->getId(),
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
			return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}
}
