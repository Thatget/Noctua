<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use SIT\ProductCompatibility\Setup\ProductCompatibilitySetup;

class UpgradeData implements UpgradeDataInterface {
	protected $eavSetupFactory;

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
			$entityType = $eavSetup->getEntityTypeId(ProductCompatibilitySetup::ENTITY_TYPE_CODE);

			$data = [
				'entity_type_code' => ProductCompatibilitySetup::ENTITY_TYPE_CODE,
				'entity_model' => \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility::class,
				'attribute_model' => \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute::class,
				'entity_table' => ProductCompatibilitySetup::ENTITY_TYPE_CODE,
				'entity_attribute_collection' => \SIT\ProductCompatibility\Model\ResourceModel\Attribute\Collection::class,
			];
			$eavSetup->updateEntityType(ProductCompatibilitySetup::ENTITY_TYPE_CODE, $data);
		}
		$setup->endSetup();
	}
}