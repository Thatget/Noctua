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

/**
 * Class Index
 */
class Save extends Action {

	/**
	 * @var SlideFactory
	 */
	protected $slideFactory;

	/**
	 * @param Context      $context      [description]
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

	public function execute() {
		$storeId = (int) $this->getRequest()->getParam('store_id');
		$data = $this->getRequest()->getParams();

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		$params = [];
		if ($data) {
			/* Added By MJ [For Edit Record] [01.04.2019]*/
			if (isset($data['entity_id'])) {
				$slideInstance = $this->slideFactory->create()->load($data['entity_id']);
				$slideInstance->setStoreId($storeId);
				$slideInstance->setSlideName($data['slide_name']);
				$slideInstance->setTitle($data['title']);
				$slideInstance->setIsShowTitleInSlide($data['is_show_title_in_slide']);
				$slideInstance->setIsActive($data['is_active']);
				if (isset($data['image_link'])) {
					$slideInstance->setImageLink(serialize($data['image_link']));
				}
				if (isset($data['image'][0]['name'])) {
					$slideInstance->setImage('proslider/' . $data['image'][0]['name']);
				}
				try {
					$slideInstance->save();
					$this->messageManager->addSuccessMessage(__('Record Updated Successfully.'));
					if (isset($data['back'])) {
						return $resultRedirect->setPath('*/*/edit', ['entity_id' => $slideInstance->getId(), '_current' => true]);
					}
					return $resultRedirect->setPath('*/*/');
				} catch (\Exception $e) {
					$this->messageManager->addErrorMessage($e->getMessage());
					$this->messageManager->addExceptionMessage($e, __('Something went wrong while updating the record.'));
				}
			} else {
				$slideInstance = $this->slideFactory->create();
				$slideInstance->setStoreId($storeId);
				$slideInstance->setSlideName($data['slide_name']);
				$slideInstance->setTitle($data['title']);
				$slideInstance->setIsShowTitleInSlide($data['is_show_title_in_slide']);
				$slideInstance->setIsActive($data['is_active']);
				$slideInstance->setImageLink(serialize($data['image_link']));
				$slideInstance->setImage('proslider/' . $data['image'][0]['name']);
				$params['store'] = $storeId;
				if (empty($data['entity_id'])) {
					$data['entity_id'] = null;
				} else {
					$slideInstance->load($data['entity_id']);
					$params['entity_id'] = $data['entity_id'];
				}
				$this->_eventManager->dispatch(
					'mediarocks_proslider_slider_prepare_save',
					['object' => $this->slideFactory, 'request' => $this->getRequest()]
				);
				try {
					$slideInstance->save();
					$this->messageManager->addSuccessMessage(__('Record Saved Successfully.'));
					$this->_getSession()->setFormData(false);
					if ($this->getRequest()->getParam('back')) {
						$params['entity_id'] = $slideInstance->getId();
						$params['_current'] = true;
						return $resultRedirect->setPath('*/*/edit', $params);
					}
					return $resultRedirect->setPath('*/*/');
				} catch (\Exception $e) {
					$this->messageManager->addErrorMessage($e->getMessage());
					$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
				}
			}
			/* End By MJ [For Edit Record] [01.04.2019]*/
			$this->_getSession()->setFormData($this->getRequest()->getPostValue());
			return $resultRedirect->setPath('*/*/edit', $params);
		}
		return $resultRedirect->setPath('*/*/');
	}
}