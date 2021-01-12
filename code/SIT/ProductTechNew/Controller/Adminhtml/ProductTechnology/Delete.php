<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Controller\Adminhtml\ProductTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use SIT\ProductTechNew\Model\ProductTechnologyFactory;

class Delete extends Action
{
    /**
     * @var ProductTechnologyFactory
     */
    protected $_productTechnologyFactory;

    /**
     * [__construct description]
     * @param Context                  $context                  [description]
     * @param ProductTechnologyFactory $productTechnologyFactory [description]
     * @param Filesystem               $filesystem               [description]
     * @param File                     $file                     [description]
     */
    public function __construct(
        Context $context,
        ProductTechnologyFactory $productTechnologyFactory,
        Filesystem $filesystem,
        File $file
    ) {
        $this->_productTechnologyFactory = $productTechnologyFactory;
        $this->_filesystem = $filesystem;
        $this->_file = $file;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductTechNew::producttechnology');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id', null);
        /**
         * Delte image from media folder : MD [START][19-03-2019]
         */
        $mediaRootDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'producttechnology/image/';
        /**
         * Delte image from media folder : MD [END][19-03-2019]
         */

        try {
            $productTechData = $this->_productTechnologyFactory->create()->load($id);
            if ($productTechData->getId()) {
                /**
                 * Delete image from media folder : MD [START][19-03-2019]
                 */
                $imageName = explode('/', $productTechData->getTechnologyImage());
                $firstName = substr($imageName[3], 0, 1);
                $secondName = substr($imageName[3], 1, 1);
                if ($this->_file->isExists($mediaRootDir . $firstName . '/' . $secondName . '/' . $imageName[3])) {
                    $this->_file->deleteFile($mediaRootDir . $firstName . '/' . $secondName . '/' . $imageName[3]);
                }
                /**
                 * Delete image from media folder : MD [END][19-03-2019]
                 */
                $productTechData->delete();
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
