<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Model;

use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem;
use \Magento\MediaStorage\Helper\File\Storage\Database;
use \Magento\MediaStorage\Model\File\UploaderFactory;
use \Magento\Store\Model\StoreManagerInterface;
use \Psr\Log\LoggerInterface;

/**
 * class ImageUploader
 */

class ImageUploader {

	/**
	 * @var Database
	 */
	private $coreFileStorageDatabase;

	/**
	 * @var mediaDirectory
	 */
	private $mediaDirectory;

	/**
	 * @var UploaderFactory
	 */
	private $uploaderFactory;

	/**
	 * @var StoreManagerInterface
	 */
	private $storeManager;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var baseTmpPath
	 */
	public $baseTmpPath;

	/**
	 * @var basePath
	 */
	public $basePath;

	/**
	 * @var allowedExtensions
	 */
	public $allowedExtensions;

	/**
	 * [__construct description]
	 * @param Database              $coreFileStorageDatabase [description]
	 * @param Filesystem            $filesystem              [description]
	 * @param UploaderFactory       $uploaderFactory         [description]
	 * @param StoreManagerInterface $storeManager            [description]
	 * @param LoggerInterface       $logger                  [description]
	 */
	public function __construct(
		Database $coreFileStorageDatabase,
		Filesystem $filesystem,
		UploaderFactory $uploaderFactory,
		StoreManagerInterface $storeManager,
		LoggerInterface $logger
	) {
		$this->coreFileStorageDatabase = $coreFileStorageDatabase;
		$this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		$this->uploaderFactory = $uploaderFactory;
		$this->storeManager = $storeManager;
		$this->logger = $logger;
		$this->baseTmpPath = "proslider";
		$this->basePath = "proslider";
		$this->allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
	}

	/**
	 * @param $baseTmpPath
	 */
	public function setBaseTmpPath($baseTmpPath) {
		$this->baseTmpPath = $baseTmpPath;
	}

	/**
	 * @param $basePath
	 */
	public function setBasePath($basePath) {
		$this->basePath = $basePath;
	}

	/**
	 * @param $allowedExtensions
	 */
	public function setAllowedExtensions($allowedExtensions) {
		$this->allowedExtensions = $allowedExtensions;
	}

	/**
	 * @return string baseTmpPath
	 */
	public function getBaseTmpPath() {
		return $this->baseTmpPath;
	}

	/**
	 * @return string baseTmpPath
	 */
	public function getBasePath() {
		return $this->basePath;
	}

	/**
	 * @return array allowedExtensions
	 */
	public function getAllowedExtensions() {
		return $this->allowedExtensions;
	}

	/**
	 * to find uploaded file path
	 * @param  $path      [image path]
	 * @param  $imageName [image name]
	 * @return string     [uploaded file path]
	 */
	public function getFilePath($path, $imageName) {
		return rtrim($path, '/') . '/' . ltrim($imageName, '/');
	}

	/**
	 * Move uploaded file to tmp dir.
	 * @param  image $imageName [description]
	 * @return imagename
	 */
	public function moveFileFromTmp($imageName) {
		$baseTmpPath = $this->getBaseTmpPath();
		$basePath = $this->getBasePath();
		$baseImagePath = $this->getFilePath($basePath, $imageName);
		$baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);
		try {
			$this->coreFileStorageDatabase->copyFile(
				$baseTmpImagePath,
				$baseImagePath
			);
			$this->mediaDirectory->renameFile(
				$baseTmpImagePath,
				$baseImagePath
			);
		} catch (\Exception $e) {
			throw new \Magento\Framework\Exception\LocalizedException(
				__('Something went wrong while saving the file(s).')
			);
		}
		return $imageName;
	}

	/**
	 * to save file in tmp directory
	 * @param  string $fileId [file name from ui-form]
	 * @return result         [result with response]
	 */
	public function saveFileToTmpDir($fileId) {

		$baseTmpPath = $this->getBaseTmpPath();
		$uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
		$uploader->setAllowedExtensions($this->getAllowedExtensions());
		$uploader->setAllowRenameFiles(true);
		$result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
		if (!$result) {
			throw new \Magento\Framework\Exception\LocalizedException(
				__('File can not be saved to the destination folder.')
			);
		}

		$result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
		$result['path'] = str_replace('\\', '/', $result['path']);
		$result['url'] = $this->storeManager
			->getStore()
			->getBaseUrl(
				\Magento\Framework\UrlInterface::URL_TYPE_MEDIA
			) . $this->getFilePath($baseTmpPath, $result['file']);
		$result['name'] = $result['file'];
		if (isset($result['file'])) {
			try {
				$relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
				$this->coreFileStorageDatabase->saveFile($relativePath);
			} catch (\Exception $e) {
				$this->logger->critical($e);
				throw new \Magento\Framework\Exception\LocalizedException(
					__('Something went wrong while saving the file(s).')
				);
			}
		}
		return $result;
	}
}