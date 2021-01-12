<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [24-05-2019]
 */
namespace SIT\ProductCompatibility\Model;

class FileUploader
{
    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    private $coreFileStorageDatabase;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
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
     * @var tmpFileSystem
     */
    public $tmpFileSystem;

    /**
     * [__construct description]
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase [description]
     * @param \Magento\Framework\Filesystem                      $filesystem              [description]
     * @param \Magento\MediaStorage\Model\File\UploaderFactory   $uploaderFactory         [description]
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager            [description]
     * @param \Psr\Log\LoggerInterface                           $logger                  [description]
     */
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->tmpFileSystem = $filesystem;
        $this->baseTmpPath = "importcomp/tmp/file";
        $this->basePath = "importcomp/file";
        $this->allowedExtensions = ['csv'];
    }

    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    public function moveFileFromTmp($imageName)
    {
        $tmpFp = $this->tmpFileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->mediaDirectory = $tmpFp;
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

    public function saveFileToTmpDir($fileId)
    {
        $tmpFp = $this->tmpFileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->mediaDirectory = $tmpFp;
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
