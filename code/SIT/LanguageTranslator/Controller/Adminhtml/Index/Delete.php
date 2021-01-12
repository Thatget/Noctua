<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\LanguageTranslator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Delete extends Action {
	protected $modelTranlation;

	/**
	 * @param Action\Context $context
	 * @param \SIT\LanguageTranslator\Model\Tranlation $model
	 */
	public function __construct(
		Action\Context $context,
		\SIT\LanguageTranslator\Model\Tranlation $model
	) {
		parent::__construct($context);
		$this->modelTranlation = $model;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_LanguageTranslator::index_delete');
	}

	/**
	 * Delete action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('translation_id');
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($id) {
			try {
				$model = $this->modelTranlation;
				$model->load($id);
				$model->delete();
				$this->messageManager->addSuccess(__('Record deleted'));
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
