<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\DataObject;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use SIT\GeneralNews\Setup\NewsSetup;

class News extends AbstractEntity {
	/**
	 * Admin store id
	 */
	const ADMIN_STORE_ID = 0;

	/**
	 * Store id
	 *
	 * @var int
	 */
	protected $_storeId = null;

	/**
	 * @var StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var \Magento\Framework\App\Cache\TypeListInterface
	 */
	protected $cacheType;

	/**
	 * @var \Magento\Framework\App\Cache\Frontend\Pool
	 */
	protected $cachePool;

	/**
	 * @param Context                                        $context      [description]
	 * @param StoreManagerInterface                          $storeManager [description]
	 * @param \Magento\Framework\App\Cache\TypeListInterface $cacheType    [description]
	 * @param \Magento\Framework\App\Cache\Frontend\Pool     $cachePool    [description]
	 * @param array                                          $data         [description]
	 */
	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		\Magento\Framework\App\Cache\TypeListInterface $cacheType,
		\Magento\Framework\App\Cache\Frontend\Pool $cachePool,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->setType(NewsSetup::ENTITY_TYPE_CODE);
		$this->setConnection(NewsSetup::ENTITY_TYPE_CODE . '_read', NewsSetup::ENTITY_TYPE_CODE . '_write');
		$this->_storeManager = $storeManager;
		$this->cacheType = $cacheType;
		$this->cachePool = $cachePool;
	}

	/**
	 * Set attribute set id and entity type id value
	 *
	 * @param \Magento\Framework\DataObject $customer
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function _beforeSave(\Magento\Framework\DataObject $object) {
		$object->setAttributeSetId($object->getAttributeSetId() ?: $this->getEntityType()->getDefaultAttributeSetId());
		$object->setEntityTypeId($object->getEntityTypeId() ?: $this->getEntityType()->getEntityTypeId());
		return parent::_beforeSave($object);
	}

	/**
	 * Action perform after save/duplicate records
	 *
	 * @param \Magento\Framework\DataObject $customer
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function _afterSave(\Magento\Framework\DataObject $object) {
		$this->cacheType->cleanType('eav');
		foreach ($this->cachePool as $cacheFrontend) {
			$cacheFrontend->getBackend()->clean();
		}
		return parent::_afterSave($object);
	}

	/**
	 * Action perform after delete records
	 *
	 * @param \Magento\Framework\DataObject $customer
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function _afterDelete(\Magento\Framework\DataObject $object) {
		$this->cacheType->cleanType('eav');
		foreach ($this->cachePool as $cacheFrontend) {
			$cacheFrontend->getBackend()->clean();
		}
		return parent::_afterDelete($object);
	}

	/**
	 * Return Entity Type instance
	 *
	 * @return \Magento\Eav\Model\Entity\Type
	 */
	public function getEntityType() {
		if (empty($this->_type)) {
			$this->setType(NewsSetup::ENTITY_TYPE_CODE);
		}
		return parent::getEntityType();
	}

	/**
	 * Retrieve News entity default attributes
	 *
	 * @return string[]
	 */
	protected function _getDefaultAttributes() {
		return [
			'attribute_set_id',
			'entity_type_id',
			'created_at',
			'updated_at',
		];
	}

	/**
	 * Set store Id
	 *
	 * @param integer $storeId
	 * @return $this
	 */
	public function setStoreId($storeId) {
		$this->_storeId = $storeId;
		return $this;
	}

	/**
	 * Return store id
	 *
	 * @return integer
	 */
	public function getStoreId() {
		if ($this->_storeId === null) {
			return $this->_storeManager->getStore()->getId();
		}
		return $this->_storeId;
	}

	/**
	 * Set Attribute values to be saved
	 *
	 * @param \Magento\Framework\Model\AbstractModel $object
	 * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
	 * @param mixed $value
	 * @return $this
	 */
	protected function _saveAttribute($object, $attribute, $value) {
		$table = $attribute->getBackend()->getTable();
		if (!isset($this->_attributeValuesToSave[$table])) {
			$this->_attributeValuesToSave[$table] = [];
		}

		$entityIdField = $attribute->getBackend()->getEntityIdField();
		$storeId = $object->getStoreId() ?: Store::DEFAULT_STORE_ID;
		$data = [
			$entityIdField => $object->getId(),
			'entity_type_id' => $object->getEntityTypeId(),
			'attribute_id' => $attribute->getId(),
			'value' => $this->_prepareValueForSave($value, $attribute),
			'store_id' => $storeId,
		];

		if (!$this->getEntityTable() || $this->getEntityTable() == \Magento\Eav\Model\Entity::DEFAULT_ENTITY_TABLE) {
			$data['entity_type_id'] = $object->getEntityTypeId();
		}

		if ($attribute->isScopeStore()) {
			/**
			 * Update attribute value for store
			 */
			$this->_attributeValuesToSave[$table][] = $data;
		} elseif ($attribute->isScopeWebsite() && $storeId != Store::DEFAULT_STORE_ID) {
			/**
			 * Update attribute value for website
			 */
			$storeIds = $this->_storeManager->getStore($storeId)->getWebsite()->getStoreIds(true);
			foreach ($storeIds as $storeId) {
				$data['store_id'] = (int) $storeId;
				$this->_attributeValuesToSave[$table][] = $data;
			}
		} else {
			/**
			 * Update global attribute value
			 */
			$data['store_id'] = Store::DEFAULT_STORE_ID;
			$this->_attributeValuesToSave[$table][] = $data;
		}

		return $this;
	}

	/**
	 * check url key
	 * @param string $urlKey
	 * @param bool $active
	 * @return mixed
	 */
	public function checkUrlKey($urlKey, $storeId, $active = true) {
		$connection = $this->getConnection();
		$stores = [self::ADMIN_STORE_ID, $storeId];
		$select = $this->_initCheckUrlKeySelect($urlKey, $stores);
		if (!$select) {
			return false;
		}
		$select->reset(\Zend_Db_Select::COLUMNS)
			->columns('e.entity_id')
			->limit(1);
		return $connection->fetchOne($select);
	}

	/**
	 * init the check select
	 */
	protected function _initCheckUrlKeySelect($urlKey, $store) {
		$connection = $this->getConnection();
		$urlRewrite = $this->_eavConfig->getAttribute(NewsSetup::ENTITY_TYPE_CODE, 'url_key');
		if (!$urlRewrite || !$urlRewrite->getId()) {
			return false;
		}
		$table = $urlRewrite->getBackend()->getTable();
		$select = $connection->select()
			->from(['e' => $table])
			->where('e.attribute_id = ?', $urlRewrite->getId())
			->where('e.value = ?', $urlKey)
			->where('e.store_id IN (?)', $store)
			->order('e.store_id DESC');
		return $select;
	}
}
