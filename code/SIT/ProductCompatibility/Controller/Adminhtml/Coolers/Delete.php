<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Coolers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;

class Delete extends Action {
	/**
	 * @var ProductCompatibilityFactory
	 */
	protected $productcompFactory;

	/**
	 * [__construct description]
	 * @param Context                     $context            [description]
	 * @param ProductCompatibilityFactory $productcompFactory [description]
	 */
	public function __construct(
		Context $context,
		ProductCompatibilityFactory $productcompFactory
	) {
		$this->productcompFactory = $productcompFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductCompatibility::coolers');
	}

	/**
	 * Delete action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$resultRedirect = $this->resultRedirectFactory->create();
		$id = $this->getRequest()->getParam('entity_id', null);

		try {
			$productcompInstance = $this->productcompFactory->create()->load($id);
			if ($productcompInstance->getId()) {
				$productcompInstance->delete();
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
