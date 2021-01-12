<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [10-04-2019]
 */
namespace Onibi\StoreLocator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Onibi\StoreLocator\Model\ResourceModel\Store\CollectionFactory;

class GridToCsv extends Action
{
    /**
     * CollectionFactory
     */
    protected $storeLocatorFactory;
    /**
     * FileFactory
     */
    protected $fileFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context $context             [description]
     * @param CollectionFactory                   $storeLocatorFactory [description]
     * @param FileFactory                         $fileFactory         [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CollectionFactory $storeLocatorFactory,
        FileFactory $fileFactory
    ) {
        $this->storeLocatorFactory = $storeLocatorFactory;
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
         * For selected row export functionality [START]
         */
        $jobData = $this->storeLocatorFactory->create();

        foreach ($jobData as $value) {
            $locatorId[] = $value['entity_id'];
        }
        $parameterData = $this->getRequest()->getParams();
        $selectedAppsid = $this->getRequest()->getParams();
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
         * For selected row export functionality [END]
         */
        $this->_view->loadLayout(false);
        $fileName = "Stores(" . date('Y-m-d') . ").csv";
        $exportBlock = $this->storeLocatorFactory->create()->addFieldToSelect('*');
        /**
         * For selected row export functionality [START]
         */
        if ($selectedAppsid != 'false') {
            $exportBlock->addFieldToFilter('entity_id', ['in' => $selectedAppsid]);
        }
        /**
         * For selected row export functionality [END]
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
                    if ($fieldName == 'country_id') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName != 'entity_id' && $fieldName != 'status' && $fieldName != 'country_id') {
                        $exportBlock->addFieldToFilter($fieldName, ['like' => '%' . $value . '%']);
                    }
                }
            }
        }
        /**
         * For filter export functionality [MD][END][11-05-2019]
         */
        $this->_fileFactory = $this->fileFactory;
        $csvData = "entity_id,status,name,store_url,store_href,store_email,address,zipcode,city,country_id,type,phone,fax,description,lat,long,image,marker\n";
        foreach ($exportBlock as $key => $value) {
            $csvData .= $value->getEntityId() . ',' . $value->getStatus() . ',"' . $value->getName() . '","' . $value->getStoreUrl() . '","' . $value->getStoreHref() . '","' . $value->getStoreEmail() . '","' . $value->getAddress() . '","' . $value->getZipcode() . '","' . $value->getCity() . '","' . $value->getCountryId() . '","' . $value->getType() . '","' . $value->getPhone() . '","' . $value->getFax() . '","' . $value->getDescription() . '","' . $value->getLat() . '","' . $value->getLong() . '","' . $value->getImage() . '","' . $value->getMarker() . '"' . "\n";
        }

        return $this->_fileFactory->create(
            $fileName,
            $csvData,
            DirectoryList::VAR_DIR
        );
    }
}
