<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface {
	// const OLD_EAV_ENTITY_TYPE_SLIDER = 'mediarocks_proslider_slider';

	// const OLD_EAV_ENTITY_TYPE_SLIDE = 'mediarocks_proslider_slide';

	protected $eavSetupFactory;

	protected $eavConfig;

	public function __construct(
		EavSetupFactory $eavSetupFactory,
		Config $eavConfig
	) {
		$this->eavSetupFactory = $eavSetupFactory;
		$this->eavConfig = $eavConfig;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

		// Added Slider Setup
		$setup->startSetup();
		/** @var EavSetup $eavSetup */
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			// $eavSetup->addAttribute(\Mediarocks\ProSlider\Setup\ProsliderSlideSetup::ENTITY_TYPE_CODE_SL,
			// 	'image_link',
			// 	[
			// 		'group' => 'General',
			// 		'type' => 'varchar',
			// 		'label' => 'Slide Image Link',
			// 		'input' => 'text',
			// 		'global' => ScopedAttributeInterface::SCOPE_STORE,
			// 		'required' => '1',
			// 		'user_defined' => false,
			// 		'default' => '',
			// 		'unique' => false,
			// 		'position' => '10',
			// 		'note' => '',
			// 		'visible' => '1',
			// 		'wysiwyg_enabled' => '0',
			// 	]
			// );
			// $attribute = $this->eavConfig->getAttribute(\Mediarocks\ProSlider\Setup\ProsliderSlideSetup::ENTITY_TYPE_CODE_SL, 'image_link');
			// $attribute->save();

			// $slider = $eavSetup->getEntityTypeId(self::OLD_EAV_ENTITY_TYPE_SLIDER);

			// $eavSetup->removeEntityType($slider);

			// $slide = $eavSetup->getEntityTypeId(self::OLD_EAV_ENTITY_TYPE_SLIDE);

			// $eavSetup->removeEntityType($slide);
		}
		$setup->endSetup();
		// // End Slider Setup
	}
}