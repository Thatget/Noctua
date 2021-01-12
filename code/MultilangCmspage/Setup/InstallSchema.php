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
namespace Emipro\MultilangCmspage\Setup;

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
        $qry = "insert into {$setup->getTable('cms_page_store')}
		select cmp.page_id,0 from {$setup->getTable('cms_page')}  cmp
		inner join {$setup->getTable('cms_page_store')} cmp1 on cmp1.page_id=cmp.page_id
		where cmp.identifier not in
		(select identifier from {$setup->getTable('cms_page')}  where page_id  in
		(select cm.page_id from {$setup->getTable('cms_page')} cm
		inner join {$setup->getTable('cms_page')} cm1 on cm.page_id=cm1.page_id
		inner join {$setup->getTable('cms_page_store')}  cmp on cm.page_id=cmp.page_id
		where cm.identifier=cm1.identifier and cmp.store_id=0))
		and cmp1.store_id in ($id) group by cmp.identifier;";

        $installer->run($qry);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('cms_page_track'))
            ->addColumn('id', Table::TYPE_SMALLINT, null, ['identity' => true, 'nullable' => false, 'auto_increment' => true, 'primary' => true], 'ID')
            ->addColumn('new_page_id', Table::TYPE_SMALLINT, 255, [], 'New Page Id')
            ->addColumn('old_page_id', Table::TYPE_SMALLINT, 255, [], 'Old Page Id')
            ->addColumn('store_id', Table::TYPE_SMALLINT, 255, [], 'Store Id')
            ->setComment('Cms Page Track');
        $installer->getConnection()->createTable($table);

        $insertQry = "insert into {$setup->getTable('cms_page_track')}(new_page_id,old_page_id,store_id)
		SELECT cp.page_id as new_page_id,
		(select cp2.page_id from {$setup->getTable('cms_page')} cp2 inner join {$setup->getTable('cms_page_store')} cps2 on cps2.page_id=cp2.page_id where cp2.identifier like cp.identifier and cps2.store_id=0 group by cp.identifier) as old_page_id,
		cps.store_id as store_id FROM {$setup->getTable('cms_page')} cp inner join {$setup->getTable('cms_page_store')} cps
		on cps.page_id = cp.page_id;";
        $installer->run($insertQry);
        $installer->endSetup();
    }

}
