<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\Adminhtml\ProductReview;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileIo;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context        $context       [description]
     * @param \Magento\Catalog\Model\ImageUploader       $imageUploader [description]
     * @param \Magento\Framework\Filesystem              $filesystem    [description]
     * @param \Magento\Framework\Filesystem\Io\File      $fileIo        [description]
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager  [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
        $this->filesystem = $filesystem;
        $this->fileIo = $fileIo;
        $this->storeManager = $storeManager;
    }

    /**
     * Upload file controller action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $imageUploadId = $this->_request->getParam('param_name', 'review_image');
        try {
            $imageResult = $this->imageUploader->saveFileToTmpDir($imageUploadId);

            /**
             * Upload image folder wise : RH [START][19-03-2019]
             */
            $imageName = $imageResult['name'];
            $firstName = substr($imageName, 0, 1);
            $secondName = substr($imageName, 1, 1);

            $basePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'productreview/image/';
            $mediaRootDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'productreview/image/' . $firstName . '/' . $secondName . '/';

            if (!is_dir($mediaRootDir)) {
                $this->fileIo->mkdir($mediaRootDir, 0775);
            }
            /**
             * Set image name with new name, If image already exist [MD][14-05-2019]
             */
            $newImageName = $this->imageNewName($mediaRootDir, $imageName);
            $this->fileIo->mv($basePath . $imageName, $mediaRootDir . $newImageName);

            /**
             * Upload image folder wise : RH [END][19-03-2019]
             */

            $imageResult['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $imageResult['name'] = $newImageName;
            $imageResult['file'] = $newImageName;
            $imageResult['url'] = $mediaUrl . 'productreview/image/' . $firstName . '/' . $secondName . '/' . $newImageName;
        } catch (\Exception $e) {
            $imageResult = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($imageResult);
    }
    /**
     * Set image name with new name, If image already exist [MD][14-05-2019]
     */
    public function imageNewName($path, $fileName)
    {
        if ($pos = strrpos($fileName, '.')) {
            $name = substr($fileName, 0, $pos);
            $ext = substr($fileName, $pos);
        } else {
            $name = $fileName;
        }
        $newpath = $path . '/' . $fileName;
        $newname = $fileName;
        $counter = 0;
        while (file_exists($newpath)) {
            $newname = $name . '_' . $counter . $ext;
            $newpath = $path . '/' . $newname;
            $counter++;
        }
        return $newname;
    }
}
