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
use Magento\Backend\App\Action\Context;
use SIT\GeneralNews\Model\NewsFactory;

class Delete extends Action {
	/**
	 * @var NewsFactory
	 */
	protected $generalnewsFactory;

	/**
	 * @param Context     $context            [description]
	 * @param NewsFactory $generalnewsFactory [description]
	 */
	public function __construct(
		Context $context,
		NewsFactory $generalnewsFactory
	) {
		$this->generalnewsFactory = $generalnewsFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_GeneralNews::news');
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
			$newsData = $this->generalnewsFactory->create()->load($id);
			if ($newsData->getId()) {
				$newsData->delete();
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
