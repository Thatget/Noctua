<?php
namespace SIT\TemplateText\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use SIT\TemplateText\Setup\TemplateTextSetup;

class UpgradeData implements UpgradeDataInterface {
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
			$entityType = $eavSetup->getEntityTypeId(TemplateTextSetup::ENTITY_TYPE_CODE);

			$data = [
				'entity_type_code' => TemplateTextSetup::ENTITY_TYPE_CODE,
				'entity_model' => \SIT\TemplateText\Model\ResourceModel\TemplateText::class,
				'attribute_model' => \SIT\TemplateText\Model\ResourceModel\Eav\Attribute::class,
				'entity_table' => TemplateTextSetup::ENTITY_TYPE_CODE,
				'entity_attribute_collection' => \SIT\TemplateText\Model\ResourceModel\Attribute\Collection::class,
			];
			$eavSetup->updateEntityType(TemplateTextSetup::ENTITY_TYPE_CODE, $data);
		}
		$setup->endSetup();
	}
}
