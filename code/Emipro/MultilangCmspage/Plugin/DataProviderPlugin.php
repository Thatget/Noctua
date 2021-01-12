<?php
namespace Emipro\MultilangCmspage\Plugin;

class DataProviderPlugin extends \Magento\Cms\Model\Page\DataProvider
{
    protected $request;

    public function getData()
    {
        $connection = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Framework\App\ResourceConnection')->getConnection('default');
        $tableStore = $connection->getTableName('cms_page_store');
        $tablePageTrack = $connection->getTableName('cms_page_track');
        $_request = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Request\Http');
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $store_id = $_request->getParam("store", 0);
        $id = $_request->getParam("page_id");
        if ($id) {
            $this->collection->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->join(array("store" => $tableStore), "main_table.page_id=store.page_id")->where("main_table.page_id in (select new_page_id from $tablePageTrack where old_page_id in (select old_page_id from $tablePageTrack where new_page_id=$id) and (store_id=" . $store_id . "))");
            $this->collection->setOrder('main_table.page_id', "DESC")->setPageSize(1);
        }
        $items = $this->collection->getItems();
        if (!count($items) && $id) {
            $this->collection = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Cms\Model\Page')->getCollection();
            $this->collection->getSelect()
                ->join(["store" => $tableStore], "main_table.page_id=store.page_id")
                ->where("main_table.page_id in (select new_page_id from $tablePageTrack where old_page_id in (select old_page_id from $tablePageTrack where new_page_id=$id) and (store_id=0))");
            $this->collection->setOrder('main_table.page_id', "DESC")->setPageSize(1);
            $items = $this->collection->getItems();
        }

        /** @var $page \Magento\Cms\Model\Page */
        //tom fixed 8/7/2020
        if ($id){
            foreach ($items as $page) {
                $this->loadedData[$id] = $page->getData();
            }
        }//end tom fix
        $data = $this->dataPersistor->get('cms_page');
        if (!empty($data)) {
            $page = $this->collection->getNewEmptyItem();
            $page->setData($data);
            $this->loadedData[$page->getId()] = $page->getData();
            $this->dataPersistor->clear('cms_page');
        }
        return $this->loadedData;
    }

}
