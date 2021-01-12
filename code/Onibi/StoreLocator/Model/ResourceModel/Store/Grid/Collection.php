<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Model\ResourceModel\Store\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\Document as StoreModel;
use Onibi\StoreLocator\Model\ResourceModel\Store\Collection as StoreCollection;

class Collection extends StoreCollection implements \Magento\Framework\Api\Search\SearchResultInterface {
	/**
	 * @var [aggregations]
	 */
	protected $aggregations;

	/**
	 * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
	 * @param \Magento\Framework\Event\ManagerInterface $eventManager
	 * @param null|\Zend_Db_Adapter_Abstract $mainTable
	 * @param string $eventPrefix
	 * @param string $eventObject
	 * @param string $resourceModel
	 * @param string $model
	 * @param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection
	 * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
	 *
	 */

	public function __construct(
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		$mainTable,
		$eventPrefix,
		$eventObject,
		$resourceModel,
		$model = StoreModel::class,
		$connection = null,
		\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	) {
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
		$this->_eventPrefix = $eventPrefix;
		$this->_eventObject = $eventObject;
		$this->_init($model, $resourceModel);
		$this->setMainTable($mainTable);
	}

	public function getAggregations() {
		return $this->aggregations;
	}
	public function setAggregations($aggregations) {
		$this->aggregations = $aggregations;
	}
	public function getAllIds($limit = null, $offset = null) {
		return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
	}
	public function getSearchCriteria() {
		return null;
	}
	public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null) {
		return $this;
	}
	public function getTotalCount() {
		return $this->getSize();
	}
	public function setTotalCount($totalCount) {
		return $this;
	}
	public function setItems(array $items = null) {
		return $this;
	}
}
