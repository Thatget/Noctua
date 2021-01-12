<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emipro\MultilangCmsblock\Model\ResourceModel;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;

/**
 * CMS block model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Block extends \Magento\Cms\Model\ResourceModel\Block {

	/**
	 * Check for unique of identifier of block to selected store(s).
	 *
	 * @param AbstractModel $object
	 * @return bool
	 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
	 */
	public function getIsUniqueBlockToStores(AbstractModel $object) {
		$entityMetadata = $this->metadataPool->getMetadata(BlockInterface::class);
		$linkField = $entityMetadata->getLinkField();

		$stores = (array) $object->getData('store_id');
		$isDefaultStore = $this->_storeManager->isSingleStoreMode()
		|| array_search(Store::DEFAULT_STORE_ID, $stores) !== false;

		if (!$isDefaultStore) {
			$stores[] = Store::DEFAULT_STORE_ID;
		}

		$select = $this->getConnection()->select()
			->from(['cb' => $this->getMainTable()])
			->join(
				['cbs' => $this->getTable('cms_block_store')],
				'cb.' . $linkField . ' = cbs.' . $linkField,
				[]
			)
			->where('cb.identifier = ?  ', $object->getData('identifier'))
			->where('cbs.store_id = ' . $stores[0]);

		// if (!$isDefaultStore) {
		// 	$select->where('cbs.store_id = ' . $stores[0]);
		// }

		if ($object->getId()) {
			$select->where('cb.' . $entityMetadata->getIdentifierField() . ' <> ?', $object->getId());
		}

		if ($this->getConnection()->fetchRow($select)) {
			return false;
		}

		return true;
	}
}
