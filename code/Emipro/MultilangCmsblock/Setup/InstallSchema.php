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
namespace Emipro\MultilangCmsblock\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $defaultStoreIds = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $websites = $objectManager->create("\Magento\Store\Model\Website")->getCollection();
        foreach ($websites as $website) {
            if ($website->getDefaultStore()) {
                $defaultStoreIds[] = $website->getDefaultStore()->getId();
            }
        }
        $id = implode(",", $defaultStoreIds);
        $qry = "insert into {$setup->getTable('cms_block_store')}
        select cmp.block_id,0 from {$setup->getTable('cms_block')}  cmp
        inner join {$setup->getTable('cms_block_store')} cmp1 on cmp1.block_id=cmp.block_id
        where cmp.identifier not in
        (select identifier from {$setup->getTable('cms_block')}  where block_id  in
        (select cm.block_id from {$setup->getTable('cms_block')} cm
        inner join {$setup->getTable('cms_block')} cm1 on cm.block_id=cm1.block_id
        inner join {$setup->getTable('cms_block_store')}  cmp on cm.block_id=cmp.block_id
        where cm.identifier=cm1.identifier and cmp.store_id=0))
        and cmp1.store_id in ($id) group by cmp.identifier;";

        $table = $installer->getConnection()
            ->newTable($installer->getTable('cms_block_track'))
            ->addColumn('id', Table::TYPE_SMALLINT, null, ['identity' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true], 'ID')
            ->addColumn('new_block_id', Table::TYPE_SMALLINT, 255, [], 'New Block Id')
            ->addColumn('old_block_id', Table::TYPE_SMALLINT, 255, [], 'Old Block Id')
            ->addColumn('store_id', Table::TYPE_SMALLINT, 255, [], 'Store Id')
            ->setComment('Cms Block Track');
        $installer->getConnection()->createTable($table);
        $installer->run($qry);

        $insertQry = "insert into {$setup->getTable('cms_block_track')}(new_block_id,old_block_id,store_id)
        SELECT cp.block_id as new_block_id,
        (select cp2.block_id from {$setup->getTable('cms_block')} cp2 inner join {$setup->getTable('cms_block_store')} cps2 on cps2.block_id=cp2.block_id where cp2.identifier like cp.identifier and cps2.store_id=0 group by cp.identifier) as old_block_id,
        cps.store_id as store_id FROM {$setup->getTable('cms_block')} cp inner join {$setup->getTable('cms_block_store')} cps
        on cps.block_id = cp.block_id;";
        $installer->run($insertQry);
        $installer->endSetup();
    }

}
