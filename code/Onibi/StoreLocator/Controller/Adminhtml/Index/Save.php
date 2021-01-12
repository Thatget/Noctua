<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Onibi\StoreLocator\Model\Store;

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
		Store $storeLocatorModel,
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
			$entity_id = $this->getRequest()->getParam('entity_id');

			if ($entity_id) {
				$this->storeLocatorModel->load($entity_id);
			}

			$data = $this->typeSaveData($data);
			$data = $this->imageSaveData($data);

			if (!array_key_exists('image', $data)) {
				$data['image'] = null;
			}

			if (!array_key_exists('marker', $data)) {
				$data['marker'] = null;
			}

			$this->storeLocatorModel->setData($data);
			try {
				$this->storeLocatorModel->save();
				$this->messageManager->addSuccess(__('The data has been saved.'));
				$this->adminsession->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					if ($this->getRequest()->getParam('back') == 'add') {
						return $resultRedirect->setPath('*/*/add');
					} else {
						return $resultRedirect->setPath(
							'*/*/edit',
							[
								'entity_id' => $this->storeLocatorModel->getEntityId(),
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
			return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}

	private function imageSaveData(array $rawData) {
		$data = $rawData;
		if (isset($data['image'][0]['name'])) {
			if (strpos($data['image'][0]['name'], 'storelocator') === false) {
				if ($data['image'][0]['name'] != '') {
					$data['image'] = 'storelocator/images/' . $data['image'][0]['name'];
				}
			} else {
				$data['image'] = $data['image'][0]['name'];
			}
		} else {
			if (array_key_exists('image', $data)) {
				$data['image'] = $data['image'][0]['url'];
			}
		}
		if (isset($data['marker'][0]['name'])) {
			if (strpos($data['marker'][0]['name'], 'storelocator') === false) {
				$data['marker'] = 'storelocator/markers/' . $data['marker'][0]['name'];
			} else {
				$data['marker'] = $data['marker'][0]['name'];
			}
		} else {
			if (array_key_exists('marker', $data)) {
				$data['marker'] = $data['marker'][0]['url'];
			}
		}
		return $data;
	}

	private function typeSaveData(array $rawData) {
		$data = $rawData;
		if (isset($data['type']) && $data['type'] != '') {
			$data['type'] = implode($data['type'], ',');
		}
		return $data;
	}
}
