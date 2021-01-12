<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\Adminhtml\ProductFaq;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class GridToXml extends Action
{
    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context                                    $context              [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory [description]
     * @param FileFactory                                                            $fileFactory          [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        FileFactory $fileFactory
    ) {
        $this->productFaqFactory = $productFaqFactory;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * generate xml
     *
     * @return xml file
     */
    public function execute()
    {
        /**
         * For selected row export functionality [MD][START][11-05-2019]
         */
        $jobData = $this->productFaqFactory->create();

        foreach ($jobData as $value) {
            $locatorId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams();
        $selectedAppsid = '';
        /**
         * For filter's data export [MD][START][11-05-2019]
         */
        $filters = $parameterData['filters'];
        unset($filters['placeholder']);
        $fields = [];
        foreach ($filters as $key => $value) {
            $fields[$key] = $value;
        }
        if (empty($fields)) {
            $filters = 0;
        }
        /**
         * For selected row export functionality [MD][START][11-05-2019]
         */
        if (array_key_exists("selected", $parameterData)) {
            $selectedAppsid = $parameterData['selected'];
        }
        if (array_key_exists("excluded", $parameterData)) {
            if ($parameterData['excluded'] == 'false') {
                $selectedAppsid = $locatorId;
            } else {
                $selectedAppsid = array_diff($locatorId, $parameterData['excluded']);
            }
        }
        /**
         * For selected row export functionality [MD][END][11-05-2019]
         */
        $this->_view->loadLayout(false);
        $fileName = "Productfaq(" . date('Y-m-d') . ").xml";
        $storeId = $this->getRequest()->getParams();

        if (array_key_exists('store_id', $storeId["filters"])) {
            $exportBlock = $this->productFaqFactory->create()->setStoreId($storeId["filters"]['store_id'])->addFieldToSelect('*');
        } else {
            $exportBlock = $this->productFaqFactory->create()->setStoreId(0)->addFieldToSelect('*');
        }
        /**
         * For selected row export functionality [MD][START][11-05-2019]
         */
        if ($selectedAppsid != 'false') {
            $exportBlock->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
        }
        /**
         * For selected row export functionality [MD][END][11-05-2019]
         */
        /**
         * For filter export functionality [MD][START][11-05-2019]
         */
        if ($filters != 0) {
            foreach ($fields as $fieldName => $value) {
                if ($fieldName != 'store_id') {
                    if ($fieldName == 'entity_id') {
                        $exportBlock->addFieldToFilter($fieldName, ["from" => $value['from'], "to" => $value['to']]);
                    }
                    if ($fieldName == 'status') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName != 'entity_id' && $fieldName != 'status') {
                        $exportBlock->addFieldToFilter($fieldName, ['like' => '%' . $value . '%']);
                    }
                }
            }
        }
        /**
         * For filter export functionality [MD][END][11-05-2019]
         */
        $this->_fileFactory = $this->fileFactory;
        $csvData = '<?xml version="1.0" encoding="UTF-8"?><items>';
        foreach ($exportBlock as $key => $value) {
            $csvData .= "<item>";
            $csvData .= "<entity_id><![CDATA[" . $value->getEntityId() . "]]></entity_id>";
            $csvData .= "<faq_que><![CDATA[" . $value->getFaqQue() . "]]></faq_que>";
            $csvData .= "<status><![CDATA[" . $value->getStatus() . "]]></status>";
            $csvData .= "</item>";
        }
        $csvData .= '</items>';
        return $this->_fileFactory->create(
            $fileName,
            $csvData,
            DirectoryList::VAR_DIR
        );
    }
}
