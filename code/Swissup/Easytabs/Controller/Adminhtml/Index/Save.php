<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

class Save extends \Magento\Backend\App\Action {
	/**
	 * Admin resource
	 */
	const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_product_save';

	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$data = $this->getRequest()->getPostValue();

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			/** @var \Swissup\Easytabs\Model\Entity $model */
			$model = $this->_objectManager->create('Swissup\Easytabs\Model\Entity');

			$id = $this->getRequest()->getParam('tab_id');
			if ($id) {
				$model->load($id);
			}
			if (empty($params['data']) && !empty($data['block_type'])) {
				$data['block'] = $data['block_type'];
			}
			unset($data['block_type']);

			/* Added By MJ [For visible_tab] [15.04.2019] */
			try {
				if (isset($data['remove_from_product'])) {
					$data['remove_from'] = json_encode(explode(',', preg_replace('/\,\s+/', ',', $data['remove_from_product'])));
				} else {
					$data['remove_from'] = null;
				}
				if (isset($data['remove_from_category'])) {
					$data['remove_from'] = json_encode(explode(',', preg_replace('/\,\s+/', ',', $data['remove_from_category'])));
				} else {
					$data['remove_from'] = null;
				}
				if (isset($data['product_key'])) {
					$data['visible_key'] = json_encode(explode(',', preg_replace('/\,\s+/', ',', $data['product_key'])));
				}
				if (isset($data['category_key'])) {
					$data['visible_key'] = json_encode(explode(',', preg_replace('/\,\s+/', ',', $data['category_key'])));
				}
				if (isset($data['custom_page'])) {
					$data['visible_key'] = json_encode(explode(',', preg_replace('/\,\s+/', ',', $data['custom_page'])));
				}
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Value is not in proper format. Please add ","(comma) between two values.'));
			}
			if (isset($data['cms_pages'])) {
				$data['visible_key'] = json_encode($data['cms_pages']);
			}
			/* End By MJ [For visible_tab] [15.04.2019] */

			$model->setData($data);

			if (isset($data['parameters'])) {
				$model->addData($data['parameters']);
			}

			$this->_eventManager->dispatch(
				'tab_prepare_save',
				['tab' => $model, 'request' => $this->getRequest()]
			);

			try {
				$model->save();
				$this->messageManager->addSuccess(__('Tab has been saved.'));
				$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					return $resultRedirect->setPath('*/*/edit', ['tab_id' => $model->getId(), '_current' => true]);
				}
				return $resultRedirect->setPath('*/*/');
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the tab.'));
			}

			$this->_getSession()->setFormData($data);

			return $resultRedirect->setPath('*/*/edit', ['tab_id' => $this->getRequest()->getParam('tab_id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}
}
