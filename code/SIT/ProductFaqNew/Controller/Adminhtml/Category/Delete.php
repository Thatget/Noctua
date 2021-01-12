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
use Magento\Backend\App\Action\Context;
use SIT\ProductFaqNew\Model\CategoryFactory;

class Delete extends Action {
	/**
	 * @var CategoryFactory
	 */
	protected $faqCatFactory;

	/**
	 * @param Context         $context       [description]
	 * @param CategoryFactory $faqCatFactory [description]
	 */
	public function __construct(
		Context $context,
		CategoryFactory $faqCatFactory
	) {
		$this->faqCatFactory = $faqCatFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductFaqNew::create_category');
	}

	/**
	 * Delete action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$resultRedirect = $this->resultRedirectFactory->create();
		$id = $this->getRequest()->getParam('id', null);

		try {
			$faqCatFactoryObj = $this->faqCatFactory->create()->load($id);
			if ($faqCatFactoryObj->getId()) {
				$faqCatFactoryObj->delete();
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
