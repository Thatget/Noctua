<?php
namespace SIT\GeneralTechnologiesNew\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use SIT\GeneralTechnologiesNew\Setup\GeneralTechnologySetup;

class UpgradeData implements UpgradeDataInterface {

	const OLD_EAV_ENTITY_TYPE = 'sip_generaltechnologiesnew_generaltechnology';

	/**
	 * @var EavSetupFactory
	 */
	protected $eavSetupFactory;

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
				$new_entity_type = $eavSetup->getEntityTypeId(GeneralTechnologySetup::ENTITY_TYPE_CODE);
				$data = [
					'entity_type_code' => GeneralTechnologySetup::ENTITY_TYPE_CODE,
					'entity_model' => \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology::class,
					'attribute_model' => \SIT\GeneralTechnologiesNew\Model\ResourceModel\Eav\Attribute::class,
					'entity_table' => GeneralTechnologySetup::ENTITY_TYPE_CODE,
					'entity_attribute_collection' => \SIT\GeneralTechnologiesNew\Model\ResourceModel\Attribute\Collection::class,
				];
				$eavSetup->updateEntityType(self::OLD_EAV_ENTITY_TYPE, $data);
				$eavSetup->removeEntityType($new_entity_type);
			}
		}
		$setup->endSetup();
	}
}
