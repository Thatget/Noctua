<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\LanguageTranslator\Model\ResourceModel\Tranlation\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\Document as TranlationModel;
use SIT\LanguageTranslator\Model\ResourceModel\Tranlation\Collection as TranlationCollection;

class Collection extends TranlationCollection implements \Magento\Framework\Api\Search\SearchResultInterface {
	protected $aggregations;

	// @codingStandardsIgnoreStart
	public function __construct(
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		$mainTable,
		$eventPrefix,
		$eventObject,
		$resourceModel,
		$model = TranlationModel::class,
		$connection = null,
		\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	) {
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
		$this->_eventPrefix = $eventPrefix;
		$this->_eventObject = $eventObject;
		$this->_init($model, $resourceModel);
		$this->setMainTable($mainTable);
	}
	// @codingStandardsIgnoreEnd

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
