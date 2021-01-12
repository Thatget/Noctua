<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Controller\Adminhtml\News;

use Magento\Backend\App\Action;

class Duplicate extends Action {

	/**
	 * @var \SIT\GeneralNews\Model\NewsFactory
	 */
	protected $newsFactory;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $date;

	/**
	 * @param Action\Context                              $context     [description]
	 * @param \SIT\GeneralNews\Model\NewsFactory          $newsFactory [description]
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime $date        [description]
	 */
	public function __construct(
		Action\Context $context,
		\SIT\GeneralNews\Model\NewsFactory $newsFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
	) {
		parent::__construct($context);
		$this->newsFactory = $newsFactory;
		$this->date = $date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($id) {
			try {
				$model = $this->newsFactory->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
				$newUrlKey = $this->checkUrlKey($model->getUrlKey());
				$duplicateData = $model->getData();
				$duplicateData["url_key"] = $newUrlKey;
				$duplicateData["created_at"] = $this->date->gmtDate();
				array_shift($duplicateData);
				$this->createDuplicate($duplicateData);
				$this->messageManager->addSuccess(__('Duplicate generated'));
				$editDuplicate = $this->getRequest()->getParam('edit_duplicate');
				$lastId = $this->newsFactory->create()->getCollection()->getLastItem();
				if ($editDuplicate == 1) {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				} else {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				}
				return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
			}
		}
		$this->messageManager->addError(__('Record does not exist'));
		return $resultRedirect->setPath('*/*/');
	}

	/**
	 * Create duplicate from main table
	 *
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	private function createDuplicate($data) {
		$model = $this->newsFactory->create();
		$model->setData($data);
		$model->save();
	}

	/**
	 * generate url key
	 *
	 * @param  [string] $urlKey [description]
	 * @return  string        [description]
	 */
	private function checkUrlKey($urlKey) {
		$model = $this->newsFactory->create()->getCollection()->addFieldToFilter("url_key", ["like" => "%" . $urlKey . "%"])->setOrder("entity_id", "DESC")->getFirstItem();
		$urlKey = $model->getUrlKey();

		return $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
		? $matches[1] . '-' . ($matches[2] + 1)
		: $urlKey . '-1';
	}
}
