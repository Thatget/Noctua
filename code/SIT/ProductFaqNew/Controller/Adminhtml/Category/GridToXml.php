<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-10-2019]
 */
namespace SIT\ProductFaqNew\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory;

class GridToXml extends Action
{
    /**
     * CollectionFactory
     */
    protected $faqCategoryFactory;

    /**
     * FileFactory
     */
    protected $fileFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context $context                  [description]
     * @param CollectionFactory                   $faqCategoryFactory [description]
     * @param FileFactory                         $fileFactory              [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CollectionFactory $faqCategoryFactory,
        FileFactory $fileFactory
    ) {
        $this->faqCategoryFactory = $faqCategoryFactory;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * generate csv file
     *
     * @return csv file
     */
    public function execute()
    {
        /**
         * For selected row export functionality [MD][START][15-10-2019]
         */
        $jobData = $this->faqCategoryFactory->create();

        foreach ($jobData as $value) {
            $locatorId[] = $value['id'];
        }
        $parameterData = $this->getRequest()->getParams();
        $selectedAppsid = '';
        /**
         * For filter's data export [MD][START][15-10-2019]
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
         * For selected row export functionality [MD][START][15-10-2019]
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
         * For selected row export functionality [MD][END][15-10-2019]
         */
        $this->_view->loadLayout(false);
        $fileName = "FAQCategory(" . date('Y-m-d') . ").xml";

        $exportBlock = $this->faqCategoryFactory->create()->addFieldToSelect('*');
        /**
         * For selected row export functionality [MD][START][15-10-2019]
         */
        if ($selectedAppsid != 'false') {
            $exportBlock->addFieldToFilter('id', ['in' => $selectedAppsid]);
        }
        /**
         * For selected row export functionality [MD][END][15-10-2019]
         */
        /**
         * For filter export functionality [MD][START][15-10-2019]
         */
        if ($filters != 0) {
            foreach ($fields as $fieldName => $value) {
                if ($fieldName != 'store_id') {
                    if ($fieldName == 'id') {
                        $exportBlock->addFieldToFilter($fieldName, ["from" => $value['from'], "to" => $value['to']]);
                    }
                    if ($fieldName == 'status') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName != 'id' && $fieldName != 'status') {
                        $exportBlock->addFieldToFilter($fieldName, ['like' => '%' . $value . '%']);
                    }
                }
            }
        }
        /**
         * For filter export functionality [MD][END][15-10-2019]
         */
        $this->_fileFactory = $this->fileFactory;
        $csvData = '<?xml version="1.0" encoding="UTF-8"?><items>';
        foreach ($exportBlock as $key => $value) {
            $status = $value->getStatus() == "1" ? "Enabled" : "Disabled";
            $csvData .= "<item>";
            $csvData .= "<id><![CDATA[" . $value->getId() . "]]></id>";
            $csvData .= "<cat_name><![CDATA[" . $value->getCatName() . "]]></cat_name>";
            $csvData .= "<parent_category><![CDATA[" . $value->getParentCatName() . "]]></parent_category>";
            $csvData .= "<position><![CDATA[" . $value->getPosition() . "]]></position>";
            $csvData .= "<status><![CDATA[" . $status . "]]></status>";
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
