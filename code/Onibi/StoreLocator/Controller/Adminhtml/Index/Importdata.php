<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Onibi\StoreLocator\Model\StoreFactory;

class Importdata extends \Magento\Backend\App\Action
{
    /**
     * @var StoreFactory
     */
    protected $storeLocatormodelFactory;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * [__construct description]
     * @param Action\Context              $context                  [description]
     * @param StoreFactory                $storeLocatormodelFactory [description]
     * @param \Magento\Framework\File\Csv $csvProcessor             [description]
     */
    public function __construct(
        Action\Context $context,
        StoreFactory $storeLocatormodelFactory,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        parent::__construct($context);
        $this->storeLocatormodelFactory = $storeLocatormodelFactory;
        $this->csvProcessor = $csvProcessor;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $fullPath = $data['csv_import'][0]['path'] . '/' . $data['csv_import'][0]['name'];
            $storeLocatorData = $this->csvProcessor->getData($fullPath);
            $count = 0;
            foreach ($storeLocatorData as $key => $dataRow) {
                $storeList = array_combine($storeLocatorData[0], array_values($dataRow));
                $dataStore = $this->saveStores($storeList);
                $count++;
            }

            $this->messageManager->addSuccess(__('A total of %1 Store locators updated.', $count - 1));
            $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $resultRedirect->setPath('*/*/import');
        }
        return $resultRedirect;
    }

    protected function saveStores($storeData)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeModel = $this->storeLocatormodelFactory->create();
        if ($storeData) {
            $entity_id = $storeData['entity_id'];

            if ($entity_id == '') {
                unset($storeData['entity_id']);
                $storeModel->setData($storeData);
            } else {
                $storeModel->setData($storeData);
            }

            return $storeModel->save();
        }
    }
}
