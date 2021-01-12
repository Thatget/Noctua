<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Controller\Adminhtml\GeneralTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use SIT\GeneralTechnologiesNew\Model\GeneralTechnologyFactory;

class Delete extends Action {
	/**
	 * @var GeneralTechnologyFactory
	 */
	protected $_generalTechnologyFactory;

	/**
	 * [__construct description]
	 * @param Context              $context       [description]
	 * @param GeneralTechnologyFactory $generalTechnologyFactory [description]
	 */
	public function __construct(
		Context $context,
		GeneralTechnologyFactory $generalTechnologyFactory,
		Filesystem $filesystem,
		File $file
	) {
		$this->_generalTechnologyFactory = $generalTechnologyFactory;
		$this->filesystem = $filesystem;
		$this->file = $file;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_GeneralTechnologiesNew::generaltechnology');
	}

	/**
	 * Delete action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$resultRedirect = $this->resultRedirectFactory->create();
		$id = $this->getRequest()->getParam('entity_id', null);
		/**
		 * Delete image from media folder : Rh
		 */
		$mediaRootDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'generaltechnology/image/';
		/**
		 * Delete image from media folder : Rh
		 */
		try {
			$generalTechnologiesData = $this->_generalTechnologyFactory->create()->load($id);
			if ($generalTechnologiesData->getId()) {
				/**
				 * Delete image from media folder : Rh
				 */
				$imageName = explode('/', $generalTechnologiesData->getGenTechnologyImage());
				$firstName = substr($imageName[3], 0, 1);
				$secondName = substr($imageName[3], 1, 1);
				if ($this->file->isExists($mediaRootDir . $firstName . '/' . $secondName . '/' . $imageName[3])) {
					$this->file->deleteFile($mediaRootDir . $firstName . '/' . $secondName . '/' . $imageName[3]);
				}
				/**
				 * Delete image from media folder : Rh
				 */
				$generalTechnologiesData->delete();
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
