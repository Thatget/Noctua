<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mediarocks\ProSlider\Model\SlideFactory;

class Delete extends Action {
	/**
	 * @var SlideFactory
	 */
	protected $slideFactory;

	/**
	 * [__construct description]
	 * @param Context     $context     [description]
	 * @param SlideFactory $slideFactory [description]
	 */
	public function __construct(
		Context $context,
		SlideFactory $slideFactory
	) {
		$this->slideFactory = $slideFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Mediarocks_ProSlider::slide');
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
			$slideInstance = $this->slideFactory->create()->load($id);
			if ($slideInstance->getId()) {
				$slideInstance->delete();
				$this->messageManager->addSuccessMessage(__('You deleted the record.'));
			} else {
				$this->messageManager->addErrorMessage(__('Record does not exist.'));
			}
		} catch (\Exception $exception) {
			$this->messageManager->addErrorMessage($exception->getMessage());
		}

		return $resultRedirect->setPath('*/slide/');
	}
}
