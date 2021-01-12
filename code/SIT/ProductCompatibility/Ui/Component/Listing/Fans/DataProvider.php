<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\Listing\Fans;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory;

class DataProvider extends UiDataProvider
{
    /**
     * sit_productcompatibility_productcompatibility_product
     */
    const PRODUCT_TABLE = 'sit_productcompatibility_productcompatibility_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * [__construct description]
     * @param [type]                $name                  [description]
     * @param Reporting             $reporting             [description]
     * @param SearchCriteriaBuilder $searchCriteriaBuilder [description]
     * @param RequestInterface      $request               [description]
     * @param FilterBuilder         $filterBuilder         [description]
     * @param ProductCompHelper     $prodCompHelper        [description]
     * @param array                 $meta                  [description]
     * @param array                 $data                  [description]
     */
    public function __construct(
        $name,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        ResourceConnection $resource,
        FilterBuilder $filterBuilder,
        ProductCompHelper $prodCompHelper,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            '',
            '',
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->name = $name;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->prodCompHelper = $prodCompHelper;
    }
    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $gridType = $this->request->getParam('comptype');
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        $optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($gridType));
        $searchResult->setStoreId($this->request->getParam('store', 0))->addAttributeToSelect('*')->addAttributeToFilter('comp_type', ['eq' => $optionId]);

        /**
         * Changes PP [15-5-2019] : Product Name and Template Text Filter Logic in Ram Compatibility
         */
        if (is_array($this->request->getParam('filters'))) {
            if (array_key_exists('template_text', $this->request->getParam('filters'))) {
                $filterParams = $this->request->getParam('filters');
                $textId = $filterParams['template_text'];
                $searchResult->addAttributeToFilter(
                    [
                        ['attribute' => 'template_text_1', 'like' => $textId],
                        ['attribute' => 'template_text_2', 'like' => $textId],
                        ['attribute' => 'template_text_3', 'like' => $textId],
                        ['attribute' => 'template_text_4', 'like' => $textId],
                    ]
                );
            }
            if (array_key_exists('product_name', $this->request->getParam('filters'))) {
                $filterParams = $this->request->getParam('filters');
                $productIds = implode(",", $filterParams['product_name']);
                $searchResult->getSelect()->joinLeft(
                    ['productTable' => self::PRODUCT_TABLE],
                    'e.entity_id = productTable.productcompatibility_id',
                    ['productTable.product_id']
                )->where('productTable.product_id IN (' . $productIds . ')')->group("e.entity_id");
            }
            /**
             * Filter for model autocomplete
             */
            if (array_key_exists('comp_model', $this->request->getParam('filters'))) {
                $filterParams = $this->request->getParam('filters');
                $compModelLabel = $filterParams['comp_model'];
                $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
                $compmodelId = $modelInfo->getAttributeId();
                $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($compmodelId);
                $ids = $this->prodCompHelper->compareArrayValues($modelAllOption, $compModelLabel);
                $searchResult->addAttributeToFilter('comp_model', ['in' => $ids]);
            }

            /**
             * Filter for manufacture autocomplete
             */
            if (array_key_exists('comp_manufacture', $this->request->getParam('filters'))) {
                $filterParams = $this->request->getParam('filters');
                $compManufLabel = $filterParams['comp_manufacture'];
                $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
                $manufId = $modelInfo->getAttributeId();
                $manufAllOption = $this->prodCompHelper->getAttributeOptionAll($manufId);
                $ids = $this->prodCompHelper->compareArrayValues($manufAllOption, $compManufLabel);
                $searchResult->addAttributeToFilter('comp_manufacture', ['in' => $ids]);
            }
        }
        /**
         * Changes PP [15-5-2019] :  Product Name and Template Text Filter Logic in Ram Compatibility
         */

        /**
         * Changed by MD for get Case Fans Compatibility Collection[START][30-05-2019]
         */
        $searchResult->getSelect()->joinLeft(
            ['t2' => self::PRODUCT_TABLE],
            'e.entity_id = t2.productcompatibility_id',
            't2.product_id'
        );
        $searchResult->getSelect()->joinLeft(
            ['ce' => 'catalog_product_entity'],
            'ce.entity_id = t2.product_id', 't2.product_id')->where('ce.attribute_set_id = 36');

        $searchResult->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.value SEPARATOR \',\'),\',\',4) as pname'
        );

        $searchResult->getSelect()->group('e.entity_id');
        /**
         * Changed by MD for get Case Fans Compatibility Collection[END][30-05-2019]
         */
        /**
         * Changed by MD for Unused Compatibility[START][23-05-2019]
         */
        $unusedComp = $this->request->getParam('unused');
        if ($unusedComp == 'unusedfans') {
            $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            $sql = 'SELECT GROUP_CONCAT(product_id) product_ids FROM  `sit_productcompatibility_productcompatibility_product` WHERE productcompatibility_id = :productcompatibility_id';
            $products_stmt = $connection->prepare($sql);
            $compatibilities = $this->collectionFactory->create();
            $idsToDisplay = [];
            foreach ($compatibilities as $comp) {
                $products_stmt->execute([
                    ':productcompatibility_id' => $comp->getId(),
                ]);
                $products = $products_stmt->fetch();
                $product_ids = $products['product_ids'];
                if (trim($product_ids) == '') {
                    $idsToDisplay[] = $comp->getId();
                }
            }
            $searchResult->addAttributeToFilter("entity_id", ["in" => $idsToDisplay]);
        }
        /**
         * Changed by MD for Unused Compatibility[END][23-05-2019]
         */
        return parent::searchResultToOutput($searchResult);
    }

    /**
     * Changes PP [15-5-2019] : Product Name Filter Logic in Ram Compatibility
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        /**
         * If you want to add filter which is custom column, then we need to add here as like product_name, template_text and comp_model, comp_manufacture
         */
        if ($filter->getField() == 'product_name' || $filter->getField() == 'template_text' || $filter->getField() == 'comp_model' || $filter->getField() == 'comp_manufacture') {
        } else {
            parent::addFilter($filter);
        }
        /**
         * If you want to add filter which is custom column, then we need to add here as like product_name and template_text
         */
    }
    /**
     * Changes PP [15-5-2019] : Product Name Filter Logic in Ram Compatibility
     */

    /**
     * @return void
     */
    protected function prepareUpdateUrl()
    {
        $storeId = $this->request->getParam('store', 0);
        if ($storeId) {
            $this->data['config']['update_url'] = sprintf(
                '%s%s/%s',
                $this->data['config']['update_url'],
                'store',
                $storeId
            );
        }
        return parent::prepareUpdateUrl();
    }
}
