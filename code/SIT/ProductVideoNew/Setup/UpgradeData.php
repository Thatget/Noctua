<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use SIT\ProductVideoNew\Setup\ProductVideoSetup;
use SIT\ProductVideoNew\Setup\ProductVideoSetupFactory;

class UpgradeData implements UpgradeDataInterface {

	const OLD_EAV_ENTITY_TYPE = 'sip_productvideonew_productvideo';
	/**
	 * @var EavSetupFactory
	 */
	protected $_exampleFactory;

    /**
     * ProductFaqNew setup factory
     *
     * @var ProductVideoSetupFactory
     */
    protected $productvideonewSetupFactory;
	/**
	 * [__construct description]
	 * @param EavSetupFactory $eavSetupFactory [description]
	 */
	public function __construct(
		EavSetupFactory $eavSetupFactory,
        ProductVideoSetupFactory $productvideonewSetupFactory
	) {
		$this->eavSetupFactory = $eavSetupFactory;
		$this->productvideonewSetupFactory = $productvideonewSetupFactory;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		/** @var EavSetup $eavSetup */
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$entityType = $eavSetup->getEntityTypeId(self::OLD_EAV_ENTITY_TYPE);
			if ($entityType) {
				$new_entity_type = $eavSetup->getEntityTypeId(ProductVideoSetup::ENTITY_TYPE_CODE);
				$data = [
					'entity_type_code' => ProductVideoSetup::ENTITY_TYPE_CODE,
					'entity_model' => \SIT\ProductVideoNew\Model\ResourceModel\ProductVideo::class,
					'attribute_model' => \SIT\ProductVideoNew\Model\ResourceModel\Eav\Attribute::class,
					'entity_table' => ProductVideoSetup::ENTITY_TYPE_CODE,
					'entity_attribute_collection' => \SIT\ProductVideoNew\Model\ResourceModel\Attribute\Collection::class,
				];
				$eavSetup->updateEntityType(self::OLD_EAV_ENTITY_TYPE, $data);
				$eavSetup->removeEntityType($new_entity_type);
			}
		}
		$setup->endSetup();

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            // update setup
            $productvideoSetup = $this->productvideonewSetupFactory->create(['setup' => $setup]);

            $setup->startSetup();

            $productvideoSetup->installEntities();
            $entities = $productvideoSetup->getDefaultEntities();
            foreach ($entities as $entityName => $entity) {
                $productvideoSetup->addEntityType($entityName, $entity);
            }

            $setup->endSetup();
        }
        if (version_compare($context->getVersion(), '1.0.0', '>') && version_compare($context->getVersion(), '1.0.4', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $idAtt = $eavSetup->getAttributeId('sit_productvideonew_productvideo', 'video_language');
            $setup->startSetup();
            $tableTmp = 'eav_attribute_option_value_tmp';
            $tableOpvl = 'eav_attribute_option_value';
            $tableOp = 'eav_attribute_option';
            $tablePrInt = 'sit_productvideonew_productvideo_int';

            $queryCreateTableTmp = "CREATE TABLE IF NOT EXISTS $tableTmp LIKE $tableOpvl;";
            $setup->getConnection()->query($queryCreateTableTmp);

            $queryInsert = "INSERT INTO $tableTmp(option_id, store_id, value) 
                            SELECT main.option_id, main.store_id, main.value from $tableOpvl as main
                            INNER JOIN $tableOp as op on op.option_id = main.option_id
                            WHERE op.attribute_id = $idAtt;";
            $setup->getConnection()->query($queryInsert);

            $queryDel = "DELETE  from $tableOp  
                        WHERE option_id in( 
                            SELECT gr.option_id FROM 
                                (
                                    SELECT main.option_id FROM $tableOp as main
                                    inner join $tableOpvl as vl on vl.option_id = main.option_id and vl.store_id = 0
                                    where main.attribute_id = $idAtt
                                    GROUP BY vl.value HAVING count(*) > 1
                                ) as gr
                        )";
            $setup->getConnection()->query($queryDel);
            $setup->getConnection()->query($queryDel);
            $setup->getConnection()->query($queryDel);
            $setup->getConnection()->query($queryDel);

            $sqlUpdate = "update $tablePrInt as main
                    inner join $tableTmp as vlTmp on main.value = vlTmp.option_id and vlTmp.store_id = 0
                    inner join $tableOpvl as opvl on opvl.value = vlTmp.value and opvl.store_id = 0
                    inner join $tableOp as op on opvl.option_id = op.option_id and  op.attribute_id = $idAtt
                    set main.value = opvl.option_id
                    where main.attribute_id = $idAtt;
                    ";
            $setup->getConnection()->query($sqlUpdate);

            $sqlDropTmp = "DROP TABLE $tableTmp;";
            $setup->getConnection()->query($sqlDropTmp);

            $queryCreateTableSort = "
                                    CREATE TABLE IF NOT EXISTS sort_tmp (
                                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                        option_id int(11) not null
                                    );";
            $setup->getConnection()->query($queryCreateTableSort);

            $queryInsertSort = "INSERT INTO sort_tmp(option_id) SELECT vl.option_id FROM $tableOpvl as vl 
                INNER JOIN $tableOp as op on op.option_id = vl.option_id
                where vl.store_id = 0 and op.attribute_id = $idAtt 
                ORDER BY vl.value ASC
                ";
            $setup->getConnection()->query($queryInsertSort);

            $queryUpdateSort = "UPDATE $tableOp as op inner join sort_tmp as tmp on tmp.option_id = op.option_id set op.sort_order = tmp.id;";
            $setup->getConnection()->query($queryUpdateSort);

            $sqlDropTmp = "DROP TABLE sort_tmp;";
            $setup->getConnection()->query($sqlDropTmp);

            $setup->endSetup();
            // update setup
        }
        //
	}
}
