<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\ProductCompatibility\Setup\ProductCompatibilitySetupFactory;

class InstallData implements InstallDataInterface {
	/**
	 * Mainpagevideo setup factory
	 *
	 * @var ProductCompatibilitySetupFactory
	 */
	protected $productcompatibilitySetupFactory;

	/**
	 * Init
	 *
	 * @param ProductCompatibilitySetupFactory $productcompatibilitySetupFactory
	 */
	public function __construct(ProductCompatibilitySetupFactory $productcompatibilitySetupFactory) {
		$this->productcompatibilitySetupFactory = $productcompatibilitySetupFactory;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		/** @var ProductCompatibilitySetupFactory $productcompatibilitySetupFactory */
		$productcompatibilitySetup = $this->productcompatibilitySetupFactory->create(['setup' => $setup]);

		$setup->startSetup();

		$productcompatibilitySetup->installEntities();
		$entities = $productcompatibilitySetup->getDefaultEntities();
		foreach ($entities as $entityName => $entity) {
			$productcompatibilitySetup->addEntityType($entityName, $entity);
		}

		$setup->endSetup();
	}
}
