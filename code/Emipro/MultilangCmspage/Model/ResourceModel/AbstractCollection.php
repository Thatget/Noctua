<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Emipro\MultilangCmspage\Model\ResourceModel;

use Magento\Store\Model\Store;

/**
 * Abstract collection of CMS pages and blocks
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $resultFactory;
    protected $storeManager;
    protected $_registry;
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;
    protected $_sessionManager;
    protected $request;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(

        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {

        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cms_entity_store' => $this->getTable($tableName)])
                ->where('cms_entity_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    abstract public function addStoreFilter($store, $withAdmin = true);

    /**
     * Perform adding filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

//        $this->addFilter('store', ['in' => $store], 'public');
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */

    protected function joinStoreRelationTable($tableName, $linkField)
    {
        $_request = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Request\Http');
        $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');

        $request = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\RequestInterface');
        $url = $urlInterface->getCurrentUrl();
        $editnew = "";
        $j = 0;
        $pieces = explode("/", $url);
        $len = count($pieces);
        $store_id = "BLANK";
        $i = 0;
        for ($e = 0; $e < $len; $e++) {
            if ($pieces[$e] == "store") {
                $i = 1;
            }
            if ($i == 1) {
                $r = $e;
                $store_id = $pieces[$r + 1];
                break;
            }
        }
        if (in_array("edit", $pieces) || in_array("new", $pieces) )
        {
            $j = 1;
        }

        if ($tableName == 'cms_page_store') {
            $tableStore = $this->getTable("cms_page_store");
            $tablePageTrack = $this->getTable("cms_page_track");
            $storeId = $this->storeManager->getStore()->getId();
            $store = $this->getFilter('store');
            //  used for store switching second time request
            $_pieces_switcher = "BLANK";
            $url_ref = $_request->getServer('HTTP_REFERER');
            $_pieces_switcher = explode("/", $url_ref);
            $store_id_switcher = 0;
            if ($url_ref != "") {
                $len = count($_pieces_switcher);
                for ($e = 0; $e < $len; $e++) {
                    if ($_pieces_switcher[$e] == "store") {
                        $i = 1;
                    }
                    if ($i == 1) {
                        $r = $e;
                        $store_id_switcher = $_pieces_switcher[$r + 1];
                        break;
                    }
                }
            }
            if ($store_id_switcher > 0) {
                if ($j == 0) {
                    $this->getSelect()
                        ->join(["store_table" => $this->getTable($tableName)], "main_table.page_id=store_table.page_id", array("store_id" => "store_id"))
                        ->where(
                            "main_table.page_id not in (
                         SELECT new_page_id FROM $tablePageTrack
                        where old_page_id in (SELECT page_track_old.old_page_id FROM $tablePageTrack page_track_old,$tablePageTrack page_track_new where page_track_old.id=page_track_new.id and page_track_old.new_page_id!=page_track_new.old_page_id and page_track_old.store_id=$store_id_switcher) and store_id=0) and store_table.store_id in ($store_id_switcher,0)
                        ")->group('main_table.' . $linkField);
                }
            } else {
                if ($j == 0) {
                    $idStore = (int) $this->request->getParam('store', 0);
                    $this->getSelect()->join(["store_table" => $this->getTable($tableName)], "main_table.page_id=store_table.page_id")->where("main_table.page_id in (select new_page_id from $tablePageTrack where store_id=0 or store_id = ".$idStore." group by (old_page_id))")->group("main_table.page_id");
                }
            }
        } else {
            //for cms_block_store
            //  used for store switching second time request  for block
            $_pieces_switcher = "BLANK";
            $url_ref = $_request->getServer('HTTP_REFERER');
            $url = $urlInterface->getCurrentUrl();
            $_pieces_switcher = explode("/", $url_ref);
            $len = count($_pieces_switcher);
            $i = 0;
            $store_id_switcher = 0;
            for ($e = 0; $e < $len; $e++) {
                if ($_pieces_switcher[$e] == "store") {
                    $i = 1;
                }
                if ($i == 1) {
                    $r = $e;
                    $store_id_switcher = $_pieces_switcher[$r + 1];
                    break;
                }
            }
            $tableStore = $this->getTable("cms_block_store");
            $tableBlockTrack = $this->getTable("cms_block_track");
            $storeId = $this->storeManager->getStore()->getId();
            $store = $this->getFilter('store');
            if ($store_id_switcher > 0) {

                if ($j != 1) {
                    $this->getSelect()->join(["store_table" => $this->getTable($tableName)], "main_table.block_id=store_table.block_id", array("store_id" => "store_id"))
                        ->where(
                            "main_table.block_id not in (
           SELECT new_block_id FROM $tableBlockTrack
          where old_block_id in (SELECT block_track_old.old_block_id FROM $tableBlockTrack block_track_old,$tableBlockTrack block_track_new where block_track_old.id=block_track_new.id and block_track_old.new_block_id!=block_track_new.old_block_id and block_track_old.store_id=$store_id_switcher) and store_id =0) and store_table.store_id in ($store_id_switcher,0)
                    ")->group("main_table.block_id");
                }
            } else {
                if ($j != 1) {
                    $this->getSelect()->join(["store_table" => $this->getTable($tableName)], "main_table.block_id=store_table.block_id", array('store_id' => 'store_table.store_id'))->where("main_table.block_id in (select new_block_id from $tableBlockTrack where store_id=0 group by (old_block_id))")->group("main_table.block_id");
                }
            }
        }
        parent::_renderFiltersBefore();
    }
}
