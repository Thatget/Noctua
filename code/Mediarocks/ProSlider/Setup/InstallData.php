<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [18-3-2019]
 */
namespace Mediarocks\ProSlider\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mediarocks\ProSlider\Setup\ProsliderSliderSetupFactory;
use Mediarocks\ProSlider\Setup\ProsliderSlideSetupFactory;

class InstallData implements InstallDataInterface {
	/**
	 * ProSliderSlider setup factory
	 *
	 * @var ProSliderSetupFactory
	 */
	protected $prosliderSliderSetupFactory;

	/**
	 * ProSliderSlide setup factory
	 *
	 * @var ProSliderSetupFactory
	 */
	protected $proSliderSlideSetupFactory;

	/**
	 * [__construct description]
	 * @param ProsliderSliderSetupFactory $prosliderSliderSetupFactory [description]
	 * @param ProsliderSlideSetupFactory  $prosliderSlideSetupFactory  [description]
	 */
	public function __construct(
		ProsliderSliderSetupFactory $prosliderSliderSetupFactory,
		ProsliderSlideSetupFactory $prosliderSlideSetupFactory
	) {
		$this->prosliderSliderSetupFactory = $prosliderSliderSetupFactory;
		$this->prosliderSlideSetupFactory = $prosliderSlideSetupFactory;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

		$setup->startSetup();
		// Added Slide table default entities
		$proSliderSlideSetup = $this->prosliderSlideSetupFactory->create(['setup' => $setup]);
		$proSliderSlideSetup->installEntities();
		$slideEntity = $proSliderSlideSetup->getDefaultEntities();
		foreach ($slideEntity as $entityName => $entity) {
			$proSliderSlideSetup->addEntityType($entityName, $entity);
		}
		// End Slide table default entities
		$setup->endSetup();

		$setup->startSetup();
		// Added Slider table defalut entities
		$proSliderSliderSetup = $this->prosliderSliderSetupFactory->create(['setup' => $setup]);
		$proSliderSliderSetup->installEntities();
		$sliderEntity = $proSliderSliderSetup->getDefaultEntities();
		foreach ($sliderEntity as $sliderEntityName => $sliderEntity) {
			$proSliderSliderSetup->addEntityType($sliderEntityName, $sliderEntity);
		}
		// End Slider table default entities
		$setup->endSetup();
	}
}