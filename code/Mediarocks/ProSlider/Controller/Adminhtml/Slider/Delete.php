<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mediarocks\ProSlider\Model\SliderFactory;

class Delete extends Action {
	/**
	 * @var SliderFactory
	 */
	protected $sliderFactory;

	/**
	 * @param Context     $context     [description]
	 * @param SliderFactory $sliderFactory [description]
	 */
	public function __construct(
		Context $context,
		SliderFactory $sliderFactory
	) {
		$this->sliderFactory = $sliderFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Mediarocks_ProSlider::slider');
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
			$sliderInstance = $this->sliderFactory->create()->load($id);
			if ($sliderInstance->getId()) {
				$sliderInstance->delete();
				$this->messageManager->addSuccessMessage(__('You deleted the record.'));
			} else {
				$this->messageManager->addErrorMessage(__('Record does not exist.'));
			}
		} catch (\Exception $exception) {
			$this->messageManager->addErrorMessage($exception->getMessage());
		}

		return $resultRedirect->setPath('*/slider/');
	}
}
