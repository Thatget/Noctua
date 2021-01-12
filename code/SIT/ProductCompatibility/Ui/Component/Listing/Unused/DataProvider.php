<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\Listing\Unused;

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
    const PRODUCT_TABLE = 'sit_productcompatibility_productcompatibility_product';

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    public function __construct(
        $name,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        ResourceConnection $resource,
        FilterBuilder $filterBuilder,
        ProductCompHelper $prodCompHelper,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
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
        $this->coreSession = $coreSession;
    }

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $gridType = $this->request->getParam('comptype');
        if ($gridType != '') {
            $this->coreSession->setProCompType($gridType);
        }
        if ($gridType == '') {
            $gridType = $this->coreSession->getProCompType();
        }
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
        }
        /**
         * Changes PP [15-5-2019] :  Product Name and Template Text Filter Logic in Ram Compatibility
         */
        /**
         * Changes PP [15-5-2019] :  Product Name and Template Text Filter Logic in Ram Compatibility
         */
        /**
         * Changed by MD for Unused Compatibility[START][23-05-2019]
         */
        $unusedComp = $this->request->getParam('unused');
        if ($unusedComp == 'unusedcomp') {
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
         * Changed by MD for Unused Compatibility[START][23-05-2019]
         */
        return parent::searchResultToOutput($searchResult);
    }
    /**
     * Changes PP [15-5-2019] : Product Name Filter Logic in Ram Compatibility
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        /**
         * If you want to add filter which is custom column, then we need to add here as like product_name and template_text
         */
        if ($filter->getField() == 'product_name' || $filter->getField() == 'template_text') {
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
