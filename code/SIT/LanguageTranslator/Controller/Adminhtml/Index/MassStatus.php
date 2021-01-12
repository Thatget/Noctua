<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\LanguageTranslator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\LanguageTranslator\Model\ResourceModel\Tranlation\CollectionFactory;
use SIT\LanguageTranslator\Model\Tranlation;

class MassStatus extends \Magento\Backend\App\Action {
	protected $filter;
	protected $collectionFactory;
	protected $Tranlationmodel;

	public function __construct(Context $context,
		Filter $filter,
		CollectionFactory $collectionFactory,
		Tranlation $Tranlationmodel
	) {
		$this->filter = $filter;
		$this->collectionFactory = $collectionFactory;
		$this->Tranlationmodel = $Tranlationmodel;
		parent::__construct($context);
	}

	public function execute() {
		$jobData = $this->collectionFactory->create();

		foreach ($jobData as $value) {
			$templateId[] = $value['translation_id'];
		}
		$parameterData = $this->getRequest()->getParams('status');
		$selectedAppsid = $this->getRequest()->getParams('status');
		if (array_key_exists("selected", $parameterData)) {
			$selectedAppsid = $parameterData['selected'];
		}
		if (array_key_exists("excluded", $parameterData)) {
			if ($parameterData['excluded'] == 'false') {
				$selectedAppsid = $templateId;
			} else {
				$selectedAppsid = array_diff($templateId, $parameterData['excluded']);
			}
		}
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter('translation_id', ['in' => $selectedAppsid]);
		$status = 0;
		$model = [];
		foreach ($collection as $item) {
			$this->setStatus($item->getTranslationId(), $this->getRequest()->getParam('status'));
			$status++;
		}
		$this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $status));
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		return $resultRedirect->setPath('*/*/');
	}

	/**
	 * [setStatus description]
	 * @param [type] $id    [description]
	 * @param [type] $Param [description]
	 */
	public function setStatus($id, $Param) {
		$item = $this->Tranlationmodel->load($id);
		$item->setStatus($Param)->save();
		return;
	}
}
