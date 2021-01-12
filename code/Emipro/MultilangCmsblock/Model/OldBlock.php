<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_MultilangCmsblock
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
namespace Emipro\MultilangCmsblock\Model;

use Magento\Framework\Data\ValueSourceInterface;

class OldBlock implements ValueSourceInterface
{
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Cms\Model\Block $block
    ) {
        $this->registry = $registry;
        $this->urlInterface = $urlInterface;
        $this->resourceConnection = $resourceConnection;
        $this->block = $block;
    }

    public function getValue($name)
    {
        $model = $this->registry->registry('cms_block');
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

            $tableStore = $connection->getTableName('cms_block_store');
            $tableBlockTrack = $connection->getTableName('cms_block_track');

            $collection1 = $this->block;
            $collection1 = $collection1->getCollection();
            $collection1->getSelect()
                ->join(["store" => $tableStore], "main_table.block_id=store.block_id")
                ->where("main_table.block_id in (select new_block_id from $tableBlockTrack where old_block_id in (select old_block_id from $tableBlockTrack where new_block_id=" . $model->getId() . ") and (store_id=0))");
            $collection1->setOrder('main_table.block_id', "DESC")->setPageSize(1);
            $data = $collection1->getData();
            $query = 'SELECT * FROM ' . $tableBlockTrack . ' where new_block_id=' . $model->getId();
            $results = $connection->fetchRow($query);
            $gpId = $results["old_block_id"];
            return $gpId;
        }
    }
}
