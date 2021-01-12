<?php
namespace SIT\GeneralNews\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $entityType = $eavSetup->getEntityTypeId(NewsSetup::ENTITY_TYPE_CODE);

            $data = [
                'entity_type_code' => NewsSetup::ENTITY_TYPE_CODE,
                'entity_model' => \SIT\GeneralNews\Model\ResourceModel\News::class,
                'attribute_model' => \SIT\GeneralNews\Model\ResourceModel\Eav\Attribute::class,
                'entity_table' => NewsSetup::ENTITY_TYPE_CODE,
                'entity_attribute_collection' => \SIT\GeneralNews\Model\ResourceModel\Attribute\Collection::class,
            ];
            $eavSetup->updateEntityType(NewsSetup::ENTITY_TYPE_CODE, $data);
        }
        $setup->endSetup();
    }
}
