<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\Adminhtml\ProductReview;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class GridToXls extends Action
{

    /**
     * sit product table name for join product name
     */
    const SIT_PRODUCTREVIEW_PRODUCT_TABLE = 'sit_productreviewnew_productreview_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * @var \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory
     */
    protected $reviewCollFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context                                       $context       [description]
     * @param \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewCollFactory [description]
     * @param FileFactory                                                               $fileFactory   [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SIT\ProductReviewNew\Model\ResourceModel\ProductReview\CollectionFactory $reviewCollFactory,
        FileFactory $fileFactory
    ) {
        $this->reviewCollFactory = $reviewCollFactory;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * generate excel
     *
     * @return excel file
     */
    public function execute()
    {
        /**
         * For selected row export functionality [MD][START][11-05-2019]
         */
        $jobData = $this->reviewCollFactory->create();

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
        $fileName = "Productreview(" . date('Y-m-d') . ").xls";
        $storeId = $this->getRequest()->getParams();

        if (array_key_exists('store_id', $storeId["filters"])) {
            $exportBlock = $this->reviewCollFactory->create()->setStoreId($storeId["filters"]['store_id'])->addFieldToSelect('*');
        } else {
            $exportBlock = $this->reviewCollFactory->create()->setStoreId(0)->addFieldToSelect('*');
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
                    if ($fieldName == 'product_review_priority') {
                        $exportBlock->addFieldToFilter($fieldName, ["from" => $value['from'], "to" => $value['to']]);
                    }
                    if ($fieldName == 'review_position') {
                        $exportBlock->addFieldToFilter($fieldName, ["from" => $value['from'], "to" => $value['to']]);
                    }
                    if ($fieldName == 'created_at') {
                        $exportBlock->addFieldToFilter($fieldName, ["from" => $value['from'], "to" => $value['to']]);
                    }
                    if ($fieldName == 'r_lng') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName == 'review_country') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName == 'review_startpage') {
                        $exportBlock->addFieldToFilter($fieldName, ['in' => $value]);
                    }
                    if ($fieldName != 'entity_id' && $fieldName != 'product_review_priority' && $fieldName != 'review_position' && $fieldName != 'created_at' && $fieldName != 'r_lng' && $fieldName != 'review_country' && $fieldName != 'review_startpage') {
                        $exportBlock->addFieldToFilter($fieldName, ['like' => '%' . $value . '%']);
                    }
                }
            }
        }
        /**
         * For filter export functionality [MD][END][11-05-2019]
         */

        /**
         * START : Join Query For Add Product Name : RH
         */
        $exportBlock->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTREVIEW_PRODUCT_TABLE],
            'e.entity_id = t2.productreview_id',
            't2.product_id'
        );
        $exportBlock->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.value SEPARATOR \', \'),\',\',4) as pname'
        );
        $exportBlock->getSelect()->group('e.entity_id');
        /**
         * END : Join Query For Add Product Name : RH
         */
        $this->_fileFactory = $this->fileFactory;
        $csvData = "Id,Products,Product Awards Priority,Awards Priority,Website,Review Language,Country,Review Startpage,Created Date\n";
        foreach ($exportBlock as $key => $value) {
            $csvData .= $value->getEntityId() . ',"' . $value->getPname() . '",' . $value->getProductReviewPriority() . ',' . $value->getReviewPosition() . ',"' . $value->getReviewWebsite() . '",' . $value->getRLng() . ',' . $value->getReviewCountry() . ',' . $value->getReviewStartpage() . ',' . $value->getCreatedAt() . "\n";
        }
        return $this->_fileFactory->create($fileName, $csvData, DirectoryList::VAR_DIR);
    }
}
