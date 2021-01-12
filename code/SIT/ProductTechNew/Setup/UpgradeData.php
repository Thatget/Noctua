<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use SIT\ProductTechNew\Setup\ProductTechnologySetup;

class UpgradeData implements UpgradeDataInterface {
	const OLD_EAV_ENTITY_TYPE = 'sip_producttechnew_producttechnology';
	/**
	 * @var EavSetupFactory
	 */
	protected $_exampleFactory;

	/**
	 * [__construct description]
	 * @param EavSetupFactory $eavSetupFactory [description]
	 */
	public function __construct(
		EavSetupFactory $eavSetupFactory
	) {
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

		$setup->startSetup();

		/** @var EavSetup $eavSetup */
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$entityType = $eavSetup->getEntityTypeId(self::OLD_EAV_ENTITY_TYPE);
			if ($entityType) {
				$new_entity_type = $eavSetup->getEntityTypeId(ProductTechnologySetup::ENTITY_TYPE_CODE);
				$data = [
					'entity_type_code' => ProductTechnologySetup::ENTITY_TYPE_CODE,
					'entity_model' => \SIT\ProductTechNew\Model\ResourceModel\ProductTechnology::class,
					'attribute_model' => \SIT\ProductTechNew\Model\ResourceModel\Eav\Attribute::class,
					'entity_table' => ProductTechnologySetup::ENTITY_TYPE_CODE,
					'entity_attribute_collection' => \SIT\ProductTechNew\Model\ResourceModel\Attribute\Collection::class,
				];
				$eavSetup->updateEntityType(self::OLD_EAV_ENTITY_TYPE, $data);
				$eavSetup->removeEntityType($new_entity_type);
			}
		}
		$setup->endSetup();
	}
}
