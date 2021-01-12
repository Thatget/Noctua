<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Controller\Adminhtml\ProductVideo;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class GridToCsv extends Action
{

    /**
     * @var \SIT\ProductVideoNew\Model\ResourceModel\ProductVideoNew\CollectionFactory
     */
    protected $productVideoFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context                                    $context              [description]
     * @param \SIT\ProductVideoNew\Model\ResourceModel\ProductVideoNew\CollectionFactory $productVideoFactory [description]
     * @param FileFactory                                                            $fileFactory          [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \SIT\ProductVideoNew\Model\ResourceModel\ProductVideo\CollectionFactory $productVideoFactory,
        FileFactory $fileFactory
    ) {
        $this->eavConfig = $eavConfig;
        $this->productVideoFactory = $productVideoFactory;
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
         * For selected row export functionality [MD][START][11-05-2019]
         */
        $jobData = $this->productVideoFactory->create();

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
        $fileName = "Productvideo(" . date('Y-m-d') . ").csv";
        $storeId = $this->getRequest()->getParams();
        $attribute = $this->eavConfig->getAttribute('sit_productvideonew_productvideo', 'video_language');
        $language = $attribute->getSource()->getAllOptions();

        $options = [];

        foreach ($language as $option) {
            if (!empty($option['label'])) {
                if ($option['label'] != ' ') {
                    $options[$option['value']] = [
                        'label' => $option['label'],
                    ];
                }
            }
        }

        if (array_key_exists('store_id', $storeId["filters"])) {
            $exportBlock = $this->productVideoFactory->create()->setStoreId($storeId["filters"]['store_id'])->addFieldToSelect('*');
        } else {
            $exportBlock = $this->productVideoFactory->create()->setStoreId(0)->addFieldToSelect('*');
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
                    if ($fieldName == 'video_language') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName != 'entity_id' && $fieldName != 'status' && $fieldName != 'video_language') {
                        $exportBlock->addFieldToFilter($fieldName, ['like' => '%' . $value . '%']);
                    }
                }
            }
        }
        /**
         * For filter export functionality [MD][END][11-05-2019]
         */
        $this->_fileFactory = $this->fileFactory;
        $csvData = "Id,Title,Status,Url,Reviewer Url,Language\n";
        $videoLan = '';
        foreach ($exportBlock as $key => $value) {
            $status = $value->getStatus() == "1" ? "Enabled" : "Disabled";
            if ($value->getVideoLanguage()) {
                $videoLan = $options[$value->getVideoLanguage()]['label'];
            }
            $csvData .= $value->getEntityId() . ',"' . $value->getVideoTitle() . '",' . $status . ',"' . $value->getVideoLink() . '","' . $value->getVideoReviewerUrl() . '","' . $videoLan . '"' . "\n";
        }

        return $this->_fileFactory->create(
            $fileName,
            $csvData,
            DirectoryList::VAR_DIR
        );
    }
}
