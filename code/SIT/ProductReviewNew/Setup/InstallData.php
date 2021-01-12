<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SIT\ProductReviewNew\Setup\ProductReviewSetupFactory;

class InstallData implements InstallDataInterface {
	/**
	 * ProductReviewNew setup factory
	 *
	 * @var ProductReviewSetupFactory
	 */
	protected $productreviewSetupFactory;

	/**
	 * [__construct description]
	 * @param ProductReviewSetupFactory $productreviewSetupFactory [description]
	 */
	public function __construct(ProductReviewSetupFactory $productreviewSetupFactory) {
		$this->productreviewSetupFactory = $productreviewSetupFactory;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		/** @var ProductReviewSetup $productreviewSetup */
		$productreviewSetup = $this->productreviewSetupFactory->create(['setup' => $setup]);

		$setup->startSetup();

		$productreviewSetup->installEntities();
		$entities = $productreviewSetup->getDefaultEntities();
		foreach ($entities as $entityName => $entity) {
			$productreviewSetup->addEntityType($entityName, $entity);
		}

		$setup->endSetup();
	}
}
