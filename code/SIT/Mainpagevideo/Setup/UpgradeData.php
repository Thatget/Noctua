<?php
namespace SIT\Mainpagevideo\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use SIT\Mainpagevideo\Setup\MainpagevideoSetup;
use SIT\Mainpagevideo\Setup\MainpagevideoSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Mainpagevideo setup factory
     *
     * @var MainpagevideoSetupFactory
     */
    protected $mainpagevideoSetupFactory;
    /**
     * @var
     */
    protected $_exampleFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        MainpagevideoSetupFactory $mainpagevideoSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->mainpagevideoSetupFactory = $mainpagevideoSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var MainpagevideoSetup $mainpagevideoSetup */
        $mainpagevideoSetup = $this->mainpagevideoSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $mainpagevideoSetup->installEntities();
        $entities = $mainpagevideoSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $mainpagevideoSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();

        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $entityType = $eavSetup->getEntityTypeId(MainpagevideoSetup::ENTITY_TYPE_CODE);

            $data = [
                'entity_type_code' => MainpagevideoSetup::ENTITY_TYPE_CODE,
                'entity_model' => \SIT\Mainpagevideo\Model\ResourceModel\Mainpagevideo::class,
                'attribute_model' => \SIT\Mainpagevideo\Model\ResourceModel\Eav\Attribute::class,
                'entity_table' => MainpagevideoSetup::ENTITY_TYPE_CODE,
                'entity_attribute_collection' => \SIT\Mainpagevideo\Model\ResourceModel\Attribute\Collection::class,
            ];
            $eavSetup->updateEntityType(MainpagevideoSetup::ENTITY_TYPE_CODE, $data);
        }
        $setup->endSetup();
    }
}
