<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Model\ResourceModel\ProductReview\Grid;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Eav\Model\ResourceModel\Helper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use SIT\ProductReviewNew\Model\ResourceModel\ProductReview\Collection as ProductReviewCollection;

/**
 * Class Collection
 * @package SIT\ProductReviewNew\Model\ResourceModel\ProductReview\Grid
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends ProductReviewCollection implements SearchResultInterface {

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * [__construct description]
     * @param EntityFactory          $entityFactory    [description]
     * @param LoggerInterface        $logger           [description]
     * @param FetchStrategyInterface $fetchStrategy    [description]
     * @param ManagerInterface       $eventManager     [description]
     * @param Config                 $eavConfig        [description]
     * @param ResourceConnection     $resource         [description]
     * @param EavEntityFactory       $eavEntityFactory [description]
     * @param Helper                 $resourceHelper   [description]
     * @param UniversalFactory       $universalFactory [description]
     * @param StoreManagerInterface  $storeManager     [description]
     * @param [type]                 $eventPrefix      [description]
     * @param [type]                 $eventObject      [description]
     * @param [type]                 $resourceModel    [description]
     * @param string                 $model            [description]
     * @param AdapterInterface|null  $connection       [description]
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'SIT\ProductReviewNew\Ui\Component\Listing\DataProvider\Document',
        AdapterInterface $connection = null
    ) {
        $this->productFactory = $productFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $connection
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
    }

    /**
     * Init select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['e' => $this->getEntity()->getEntityTable()]);
        $entity = $this->getEntity();
        if ($entity->getTypeId() && $entity->getEntityTable() == \Magento\Eav\Model\Entity::DEFAULT_ENTITY_TABLE) {
            $this->addAttributeToFilter('entity_type_id', $this->getEntity()->getTypeId());
        }
        $this->getSelect()->joinLeft(
            array("sit_product" => $this->getTable("sit_productreviewnew_productreview_product")),
            "sit_product.productreview_id = e.entity_id",
            array()
        );

        $this->getSelect()->joinLeft(
            array("p_name" => $this->getTable("catalog_product_entity_varchar")),
            "sit_product.product_id = p_name.entity_id AND attribute_id = " . self::PRODUCT_NAME_ATTRIBUTE_ID,
            array('product_name' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT(p_name.value))"))
        )->group("e.entity_id");

        return $this;
    }

    /**
     * Add attribute filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AttributeInterface|integer|string|array $attribute
     * @param null|string|array $condition
     * @param string $joinType
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @see self::_getConditionSql for $condition
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($attribute != "product_name") {
            return parent::addAttributeToFilter($attribute, $condition, $joinType);
        } else {
            $resultCondition = $this->_translateCondition(new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT(p_name.value))"), $condition);
            $this->getSelect()->having($resultCondition, null, Select::TYPE_CONDITION);
        }
        return $this;
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if ($this->_totalRecords === null) {
            $customCountSelect = $this->getConnection()->select();
            $customCountSelect->from(array('custom_count' => $this->getSelect()));
            $customCountSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
            $customCountSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            $this->_totalRecords = $this->getConnection()->fetchOne($customCountSelect, $this->_bindParams);
        }
        return (int)$this->_totalRecords;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations() {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations) {
        $this->aggregations = $aggregations;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null) {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria() {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null) {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount() {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount) {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null) {
        return $this;
    }
}