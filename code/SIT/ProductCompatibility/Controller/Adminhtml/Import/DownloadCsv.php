<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [28-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class DownloadCsv extends Action
{
    /**
     * FileFactory
     */
    protected $fileFactory;

    /**
     * \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context             $context          [description]
     * @param FileFactory                                     $fileFactory      [description]
     * @param \Magento\Framework\Filesystem                   $filesystem       [description]
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

    /**
     * generate csv file
     *
     * @return csv file
     */
    public function execute()
    {
        $fileName = 'sample_compatibility.csv';
        $fileAbsolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'compatibility/' . $fileName;
        if (!file_exists($fileAbsolutePath)) {
            $this->messageManager->addError(__('There is no sample file for this compatibility.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/import');
            return $resultRedirect;
        }
        $fileContent = file_get_contents($fileAbsolutePath);
        $this->fileFactory->create(
            $fileName,
            $fileContent,
            DirectoryList::VAR_DIR
        );
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($fileContent);
        return $resultRaw;
    }
}
