<?php
/**
 * Copyright © 2015 RokanThemes.com. All rights reserved.

 * @author RokanThemes Team <contact@rokanthemes.com>
 */

namespace Qsoft\ContentCustom\Model\ResourceModel;

/**
 * Blog category resource model
 */
class LinkReplace extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('contentcustom_link_replace', 'link_id');
    }

    /**
     * Process post data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['link_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('contentcustom_link_replace_store'), $condition);
        return parent::_beforeDelete($object);
    }

    /**
     * Process post data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_beforeSave($object);
    }

    /**
     * Assign post to store views, categories, related posts, etc.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStores();
        if (empty($newIds)) {
            $newIds = (array)$object->getStoreId();
        }
        $this->_updateLinks($object, $newIds, $oldIds, 'contentcustom_link_replace_store', 'store_id');

        return parent::_afterSave($object);
    }

    /**
     * Update post connections
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  Array $newRelatedIds
     * @param  Array $oldRelatedIds
     * @param  String $tableName
     * @param  String  $field
     * @return void
     */
    protected function _updateLinks(
        \Magento\Framework\Model\AbstractModel $object,
        Array $newRelatedIds,
        Array $oldRelatedIds,
        $tableName,
        $field
    ) {
        $table = $this->getTable($tableName);

        $insert = array_diff($newRelatedIds, $oldRelatedIds);
        $delete = array_diff($oldRelatedIds, $newRelatedIds);

        if ($delete) {
            $where = ['link_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['link_id' => (int)$object->getId(), $field => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
    }
    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $pageId
     * @return array
     */
    public function lookupStoreIds($postId)
    {
        return $this->_lookupIds($postId, 'contentcustom_link_replace_store', 'store_id');
    }
    /**
     * Get ids to which specified item is assigned
     * @param  int $postId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($postId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'link_id = ?',
            (int)$postId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isObjectNotNew(\Magento\Framework\Model\AbstractModel $object)
    {
        return $object->getId() !== null && $object->getId() && (!$this->_useIsObjectNew || !$object->isObjectNew());
    }
}
