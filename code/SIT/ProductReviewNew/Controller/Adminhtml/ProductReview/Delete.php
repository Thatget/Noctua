<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\Adminhtml\ProductReview;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use SIT\ProductReviewNew\Model\ProductReviewFactory;

class Delete extends Action {
	/**
	 * @var ProductReviewFactory
	 */
	protected $_reviewFactory;

	/**
	 * @var [type]
	 */
	protected $filesystem;

	/**
	 * @var [type]
	 */
	protected $file;

	/**
	 * [__construct description]
	 * @param Context              $context       [description]
	 * @param ProductReviewFactory $reviewFactory [description]
	 * @param Filesystem           $filesystem    [description]
	 * @param File                 $file          [description]
	 */
	public function __construct(
		Context $context,
		ProductReviewFactory $reviewFactory,
		Filesystem $filesystem,
		File $file
	) {
		$this->_reviewFactory = $reviewFactory;
		$this->filesystem = $filesystem;
		$this->file = $file;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('SIT_ProductReviewNew::menu');
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
		 * Delte image from media folder : MD [START][19-03-2019]
		 */
		$mediaRootDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'productreview/image/';
		/**
		 * Delte image from media folder : MD [END][19-03-2019]
		 */
		try {
			$reviewInstance = $this->_reviewFactory->create()->load($id);

			if ($reviewInstance->getEntityId()) {
				/**
				 * Delete image from media folder : MD [START][19-03-2019]
				 */
				$imageName = explode('/', $reviewInstance->getReviewImage());
				$firstName = substr($imageName[3], 0, 1);
				$secondName = substr($imageName[3], 1, 1);
				if ($this->file->isExists($mediaRootDir . $firstName . '/' . $secondName . '/' . $imageName[3])) {
					$this->file->deleteFile($mediaRootDir . $firstName . '/' . $secondName . '/' . $imageName[3]);
				}
				/**
				 * Delete image from media folder : MD [END][19-03-2019]
				 */
				$reviewInstance->delete();
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
