<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace VladimirPopov\WebFormsDepend\Controller\Adminhtml\Field;

use Magento\Backend\App\Action;

class Save extends \VladimirPopov\WebForms\Controller\Adminhtml\Field\Save {
	/**
	 * \VladimirPopov\WebForms\Helper\Data
	 */
	protected $webformsHelper;

	/**
	 * \VladimirPopov\WebForms\Model\FieldFactory
	 */
	protected $fieldFactory;

	/**
	 * [__construct description]
	 * @param Action\Context                              $context        [description]
	 * @param \VladimirPopov\WebForms\Helper\Data         $webformsHelper [description]
	 * @param \VladimirPopov\WebForms\Model\FieldFactory  $fieldFactory   [description]
	 */
	public function __construct(
		Action\Context $context,
		\VladimirPopov\WebForms\Helper\Data $webformsHelper,
		\VladimirPopov\WebForms\Model\FieldFactory $fieldFactory
	) {
		parent::__construct($context, $webformsHelper, $fieldFactory);
	}

	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$store = $this->getRequest()->getParam('store');
		$data = $this->getRequest()->getPostValue('field');

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			$model = $this->fieldFactory->create();

			/*Changed by MD for custom fields[START][29-04-2019]*/
			$webformId = $data["webform_id"];
			$coll = $model->getCollection()->addFieldToFilter('webform_id', $webformId);
			$fieldsCount = $coll->count();
			/*Changed by MD for custom fields[END][29-04-2019]*/

			!empty($data['id']) ? $id = $data['id'] : $id = $this->getRequest()->getParam('id');
			if ($id) {
				$model->load($id);

				if ($store) {
					unset($data['id']);
					unset($data['webform_id']);
					$model->saveStoreData($store, $data);
				}
			}

			isset($data['type']) ?: $data['type'] = $model->getType();
			switch ($data['type']) {
			case 'text':
				break;
			case 'email':
				if (!empty($data["hint_email"])) {
					$data["hint"] = $data["hint_email"];
				} else {
					$data["hint"] = "";
				}

				break;
			case 'url':
				if (!empty($data["hint_url"])) {
					$data["hint"] = $data["hint_url"];
				} else {
					$data["hint"] = "";
				}

				break;
			case 'textarea':
				if (!empty($data["hint_textarea"])) {
					$data["hint"] = $data["hint_textarea"];
				} else {
					$data["hint"] = "";
				}

				break;
			case 'hidden':
				if (!$this->_authorization->isAllowed('VladimirPopov_WebForms::field_hidden')) {
					$this->messageManager->addErrorMessage(__('You don\'t have permission to manage Hidden fields'));
					return $resultRedirect->setPath('*/*/edit', array('_current' => true));
				};
				break;
			case 'image':
				if (!empty($data["value"]["dropzone_image"])) {
					$data["value"]["dropzone"] = $data["value"]["dropzone_image"];
				}

				if (!empty($data["value"]["dropzone_text_image"])) {
					$data["value"]["dropzone_text"] = $data["value"]["dropzone_text_image"];
				}

				if (!empty($data["value"]["dropzone_maxfiles_image"])) {
					$data["value"]["dropzone_maxfiles"] = $data["value"]["dropzone_maxfiles_image"];
				}

				break;
			}

			$this->_eventManager->dispatch(
				'webforms_field_prepare_save',
				['field' => $model, 'request' => $this->getRequest()]
			);

			try {
				if (!$store) {
					$model->setData($data)->save();
					/*Changed by MD for custom fields[START][29-04-2019]*/
					if ($fieldsCount == 0) {
						$this->setCustomFields('Express Delivery', 'express_delivery', 'delivery', '900', '1', $webformId);
						$this->setCustomFields('Delivery Method', 'delivery_method', 'text', '901', '1', $webformId);
						$this->setCustomFields('Delivery Amount', 'amount', 'text', '902', '1', $webformId);
						$this->setCustomFields('Payment Confirmed', 'payment_confirmed', 'delivery', '903', '1', $webformId);
						$this->setCustomFields('Transaction Id', 'transaction_id', 'text', '904', '1', $webformId);
					} else {
						$codeFieldArr = [];
						foreach ($coll as $value) {
							$codeFieldArr[] = $value->getCode();
						}
						if (!(in_array('express_delivery', $codeFieldArr))) {
							$this->setCustomFields('Express Delivery', 'express_delivery', 'delivery', '900', '1', $webformId);
						}
						if (!(in_array('delivery_method', $codeFieldArr))) {
							$this->setCustomFields('Delivery Method', 'delivery_method', 'text', '901', '1', $webformId);
						}
						if (!(in_array('amount', $codeFieldArr))) {
							$this->setCustomFields('Delivery Amount', 'amount', 'text', '902', '1', $webformId);
						}
						if (!(in_array('payment_confirmed', $codeFieldArr))) {
							$this->setCustomFields('Payment Confirmed', 'payment_confirmed', 'delivery', '903', '1', $webformId);
						}
						if (!(in_array('transaction_id', $codeFieldArr))) {
							$this->setCustomFields('Transaction Id', 'transaction_id', 'text', '904', '1', $webformId);
						}
					}
					/*Changed by MD for custom fields[END][29-04-2019]*/
				}

				$this->messageManager->addSuccessMessage(__('You saved this field.'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					return $resultRedirect->setPath('*/field/edit', ['id' => $model->getId(), '_current' => true]);
				}
				return $resultRedirect->setPath('*/form/edit', ['id' => $model->getWebformId(), 'active_tab' => 'fields_section', 'store' => $store]);
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the field.'));
			}

			$this->_getSession()->setFormData($data);
			return $resultRedirect->setPath('*/*/edit', ['id' => $id, 'webform_id' => $this->getRequest()->getParam('webform_id'), 'store' => $store]);
		}
		return $resultRedirect->setPath('webforms/form/');
	}
	/*Changed by MD for custom fields[START][29-04-2019]*/
	public function setCustomFields($name, $code, $type, $position, $isActive, $webformId) {
		$model = $this->fieldFactory->create();
		$model->setData('name', $name);
		$model->setData('code', $code);
		$model->setData('type', $type);
		$model->setData('position', $position);
		$model->setData('is_active', $isActive);
		$model->setData('webform_id', $webformId);
		$model->setData('hide_label', '1');
		$model->save();
	}
	/*Changed by MD for custom fields[END][29-04-2019]*/
}
