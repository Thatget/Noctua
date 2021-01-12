<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_MultilangCmspage
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
namespace Emipro\MultilangCmspage\Model;

use Magento\Framework\Data\ValueSourceInterface;

class AllStoreUrl implements ValueSourceInterface
{
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Cms\Model\Page $page
    ) {
        $this->registry = $registry;
        $this->urlInterface = $urlInterface;
        $this->resourceConnection = $resourceConnection;
        $this->page = $page;
    }

    public function getValue($name)
    {
        $model = $this->registry->registry('cms_page');
        $urlInterface = $this->urlInterface;
        $url = $urlInterface->getCurrentUrl();
        $pieces = explode("/", $url);
        $len = count($pieces);
        $i = 0;
        $storeId = "";
        for ($e = 0; $e < $len; $e++) {
            if ($pieces[$e] == "store") {
                $i = 1;
            }
            if ($i == 1) {
                $r = $e;
                $storeId = $pieces[$r + 1];
                break;
            }
        }
        if (empty($storeId)) {
            $model["store_id"] = 0;
        } else {
            $model["store_id"] = $storeId;
        }
        if ($model->getId()) {
            $connection = $this->resourceConnection->getConnection('default');
            $tableStore = $connection->getTableName('cms_page_store');
            $tablePageTrack = $connection->getTableName('cms_page_track');
            $collection1 = $this->page;
            $collection1 = $collection1->getCollection();
            $collection1->getSelect()
                ->join(["store" => $tableStore], "main_table.page_id=store.page_id")
                ->where("main_table.page_id in (select new_page_id from $tablePageTrack where old_page_id in (select old_page_id from $tablePageTrack where new_page_id=" . $model->getId() . ") and (store_id=0))");
            $collection1->setOrder('main_table.page_id', "DESC")->setPageSize(1);
            $data = $collection1->getFirstItem();
            $allStoreUrl = $data["identifier"];
            return $allStoreUrl;
        }
    }
}
