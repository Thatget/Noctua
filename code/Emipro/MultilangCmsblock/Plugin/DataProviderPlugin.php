<?php
namespace Emipro\MultilangCmsblock\Plugin;

class DataProviderPlugin extends \Magento\Cms\Model\Block\DataProvider {
	protected $request;

	public function getData() {
		$connection = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Framework\App\ResourceConnection')->getConnection('default');
		$tableStore = $connection->getTableName('cms_block_store');
		$tableBlockTrack = $connection->getTableName('cms_block_track');
		$_request = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Request\Http');
		if (isset($this->loadedData)) {
			return $this->loadedData;
		}
		$store_id = $_request->getParam("store", 0);
		$id = $_request->getParam("block_id");
		if ($id) {
			$this->collection->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->join(["store" => $tableStore], "main_table.block_id=store.block_id")
				->where("main_table.block_id in (select new_block_id from $tableBlockTrack where old_block_id in (select old_block_id from $tableBlockTrack where new_block_id=$id) and (store_id=" . $store_id . "))");
			$this->collection->setOrder('main_table.block_id', "DESC")->setPageSize(1);
		}
		$items = $this->collection->getItems();
		if (!count($items) && $id) {
			$this->collection = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Cms\Model\Block')->getCollection();
			$this->collection->getSelect()
				->join(["store" => $tableStore], "main_table.block_id=store.block_id")
				->where("main_table.block_id in (select new_block_id from $tableBlockTrack where old_block_id in (select old_block_id from $tableBlockTrack where new_block_id=$id) and (store_id=0))");
			$this->collection->setOrder('main_table.block_id', "DESC")->setPageSize(1);
			$items = $this->collection->getItems();
		}

		if ($id){
            foreach ($items as $block) {
                $this->loadedData[$id] = $block->getData();
            }
        }

		$data = $this->dataPersistor->get('cms_block');
		if (!empty($data)) {
			$block = $this->collection->getNewEmptyItem();
			$block->setData($data);
			$this->loadedData[$block->getId()] = $block->getData();
			$this->dataPersistor->clear('cms_block');
		}

		return $this->loadedData;
	}

}
