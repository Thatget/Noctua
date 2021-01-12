<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-04-2019]
 */
namespace BL\FileAttributes\Model\Product\Attribute\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;
use SIT\MainAdmin\Helper\Data as MainAdminHelper;

class File extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend {
	/**
	 * @var \Magento\Framework\Filesystem\Driver\File
	 */
	protected $_file;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $_logger;

	/**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $_filesystem;

	/**
	 * @var \Magento\MediaStorage\Model\File\UploaderFactory
	 */
	protected $_fileUploaderFactory;

	/**
	 * @var MainAdminHelper
	 */
	protected $sitHelper;

	/**
	 * [__construct description]
	 * @param \Psr\Log\LoggerInterface                         $logger              [description]
	 * @param \Magento\Framework\Filesystem                    $filesystem          [description]
	 * @param \Magento\Framework\Filesystem\Driver\File        $file                [description]
	 * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory [description]
	 * @param MainAdminHelper                                  $sitHelper           [description]
	 */
	public function __construct(
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		MainAdminHelper $sitHelper
	) {
		$this->_file = $file;
		$this->_filesystem = $filesystem;
		$this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_logger = $logger;
		$this->sitHelper = $sitHelper;
	}

	public function afterSave($object) {
		$attName = $this->getAttribute()->getName();
		$blFilePath = '';
		if ($attName == MainAdminHelper::INFOSHEET_VARIABLE) {
			$blFilePath = 'blfa_files/infosheet/';
		} else if ($attName == MainAdminHelper::MANUAL_VARIABLE) {
			$blFilePath = 'blfa_files/manual/';
		}

		$path = $this->_filesystem->getDirectoryRead(
			DirectoryList::MEDIA
		)->getAbsolutePath($blFilePath);

		$delete = $object->getData($this->getAttribute()->getName() . '_delete');

		if ($delete) {
			$fileName = $object->getData($this->getAttribute()->getName());
			$object->setData($this->getAttribute()->getName(), '');
			$this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
			if ($this->_file->isExists($path . $fileName)) {
				$this->_file->deleteFile($path . $fileName);
			}
		}

		if (empty($_FILES)) {
			return $this; // if no image is set then nothing to do
		}

		try {
			/** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
			$uploader = $this->_fileUploaderFactory->create(['fileId' => 'product[' . $this->getAttribute()->getName() . ']']);
			$uploader->setAllowedExtensions(['pdf']);
			$uploader->setAllowRenameFiles(true);
			$result = $uploader->save($path);
			$object->setData($this->getAttribute()->getName(), $result['file']);
			$this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
		} catch (\Exception $e) {
			if ($e->getCode() != \Magento\MediaStorage\Model\File\Uploader::TMP_NAME_EMPTY) {
				$this->_logger->critical($e);
			}
		}

		return $this;
	}
}
