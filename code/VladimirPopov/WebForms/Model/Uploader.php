<?php

namespace VladimirPopov\WebForms\Model;

class Uploader
{

    const UPLOAD_DIR = 'webforms';
    // const UPLOAD_DIR = 'webforms/upload';

    protected $_result;

    protected $storeManager;

    protected $fileFactory;

    protected $random;

    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \VladimirPopov\WebForms\Model\FileFactory $fileFactory,
        \Magento\Framework\Math\Random $random
    ) {
        $this->storeManager = $storeManager;
        $this->fileFactory = $fileFactory;
        $this->random = $random;
    }

    public function setResult(\VladimirPopov\WebForms\Model\Result $result)
    {
        $this->_result = $result;
        return $this;
    }
    /**
     * @return \VladimirPopov\WebForms\Model\Result
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * @return \VladimirPopov\WebForms\Model\Form
     */
    public function getWebform()
    {
        return $this->getResult()->getWebform();
    }

    public function getUploadDir()
    {
        return $this->storeManager->getStore($this->getResult()->getStoreId())->getBaseMediaDir() . '/' . self::getPath();
    }

    public static function getPath()
    {
        return self::UPLOAD_DIR;
    }

    public function upload()
    {
        if ($this->getResult()) {
            $uploaded_files = $this->getWebform()->getUploadedFiles();

            foreach ($uploaded_files as $field_id => $file) {
                $file_id = 'file_' . $field_id;
                $uploader = new \Magento\Framework\File\Uploader($file_id);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $tmp_name = $this->random->getRandomString(20);
                $link_hash = $this->random->getRandomString(40);
                $size = filesize($file['tmp_name']);
                $mime = self::getMimeType($file['tmp_name']);
                /**
                 * Changed by PP Concat file path[START]
                 */
                $newlink_hash = $this->random->getRandomString(6);
                $concatPath = $this->getResult()->getId() . '/' . $field_id . '/' . $newlink_hash . '/';

                if (!is_dir($this->getUploadDir() . '/' . $concatPath)) {
                    mkdir($this->getUploadDir() . '/' . $concatPath, 0777, true);
                }

                $success = $uploader->save($this->getUploadDir() . '/' . $concatPath, $file['name']); // $tmp_name

                if ($success) {
                    /** @var \VladimirPopov\WebForms\Model\File $model */
                    $model = $this->fileFactory->create();

                    // remove previously uploaded file
                    $collection = $model->getCollection()
                        ->addFilter('result_id', $this->getResult()->getId())
                        ->addFilter('field_id', $field_id);
                    /** @var \VladimirPopov\WebForms\Model\File $old_file */
                    foreach ($collection as $old_file) {
                        $old_file->delete();
                    }

                    // save new file
                    $model->setData('result_id', $this->getResult()->getId())
                        ->setData('field_id', $field_id)
                        ->setData('name', $success['file']) //$file['name']
                        ->setData('size', $size)
                        ->setData('mime_type', $mime)
                        ->setData('path', $this->getPath() . '/' . $concatPath . $success['file']) //$file['name']
                        ->setData('link_hash', $newlink_hash);
                    $model->save();
                }
                /**
                 * Changed by PP Concat file path[END]
                 */
            }
        }
    }

    public static function getMimeType($path)
    {
        if (class_exists('finfo')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $type = $finfo->file($path);
            return $type;
        }
    }

}
