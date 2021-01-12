<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface {
	const OLD_EAV_ENTITY_TYPE_SLIDER = 'mediarocks_proslider_slider';

	const OLD_EAV_ENTITY_TYPE_SLIDE = 'mediarocks_proslider_slide';

	protected $eavSetupFactory;

	public function __construct(
		EavSetupFactory $eavSetupFactory
	) {
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

		// Added Slider Setup
		$setup->startSetup();
		/** @var EavSetup $eavSetup */
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		if (version_compare($context->getVersion(), '1.0.1', '<')) {

			// $slider = $eavSetup->getEntityTypeId(self::OLD_EAV_ENTITY_TYPE_SLIDER);

			// $eavSetup->removeEntityType($slider);

			// $slide = $eavSetup->getEntityTypeId(self::OLD_EAV_ENTITY_TYPE_SLIDE);

			// $eavSetup->removeEntityType($slide);
		}
		$setup->endSetup();
		// // End Slider Setup
	}
}