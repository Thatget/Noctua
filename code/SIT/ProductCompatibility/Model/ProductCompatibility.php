<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class ProductCompatibility extends AbstractModel implements IdentityInterface {
	/**
	 * CMS page cache tag
	 */
	const CACHE_TAG = 'sit_productcompatibility_mainboard';

	/**
	 * entity_type_id for save Entity Type ID value
	 */
	const KEY_ENTITY_TYPE_ID = 'entity_type_id';

	/**
	 * attribute_set_id for save Attribute Set ID value
	 */
	const KEY_ATTR_TYPE_ID = 'attribute_set_id';

	/**
	 * store id
	 */
	const STORE_ID = 'store_id';

	/**
	 * @var string
	 */
	protected $_cacheTag = 'sit_productcompatibility_mainboard';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'sit_productcompatibility_mainboard';

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(\SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility::class);
	}

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	/**
	 * Save from collection data
	 *
	 * @param array $data
	 * @return $this|bool
	 */
	public function saveCollection(array $data) {
		if (isset($data[$this->getId()])) {
			$this->addData($data[$this->getId()]);
			$this->getResource()->save($this);
		}
		return $this;
	}

	/**
	 * Set attribute set entity type id
	 *
	 * @param int $entityTypeId
	 * @return $this
	 */
	public function setEntityTypeId($entityTypeId) {
		return $this->setData(self::KEY_ENTITY_TYPE_ID, $entityTypeId);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntityTypeId() {
		return $this->getData(self::KEY_ENTITY_TYPE_ID);
	}

	/**
	 * Set attribute set id
	 *
	 * @param int $attrSetId
	 * @return $this
	 */
	public function setAttributeSetId($attrSetId) {
		return $this->setData(self::KEY_ATTR_TYPE_ID, $attrSetId);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttributeSetId() {
		return $this->getData(self::KEY_ATTR_TYPE_ID);
	}

	/**
	 * Set product store id
	 *
	 * @param int $storeId
	 * @return $this
	 */
	public function setStoreId($storeId) {
		return $this->setData(self::STORE_ID, $storeId);
	}

	/**
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @return \Magento\Catalog\Model\ResourceModel\Category
	 * @deprecated because resource models should be used directly
	 */
	protected function _getResource() {
		return parent::_getResource();
	}

	/**
	 * Retrieve default attribute set id
	 *
	 * @return int
	 */
	public function getDefaultAttributeSetId() {
		return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
	}
}
