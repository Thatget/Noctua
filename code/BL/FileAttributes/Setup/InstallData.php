<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-04-2019]
 */

namespace BL\FileAttributes\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var AttributeSet
     */
    protected $_attributeSet;

    /**
     * @var EavSetupFactory
     */

    protected $_eavSetupFactory;

    /**
     * @var ResourceProduct
     */
    protected $_resourceProduct;

    /**
     * [__construct description]
     * @param AttributeSet    $attributeSet    [description]
     * @param EavSetupFactory $eavSetupFactory [description]
     * @param ResourceProduct $resourceProduct [description]
     */
    public function __construct(
        AttributeSet $attributeSet,
        EavSetupFactory $eavSetupFactory,
        ResourceProduct $resourceProduct
    ) {
        $this->_attributeSet = $attributeSet;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_resourceProduct = $resourceProduct;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->_eavSetupFactory->create(["setup" => $setup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            'infosheet',
            [
                'group' => 'Information Sheet and Manual',
                'type' => 'varchar',
                'label' => 'Information Sheet',
                'input' => 'file',
                'backend' => 'BL\FileAttributes\Model\Product\Attribute\Backend\File',
                'frontend' => 'BL\FileAttributes\Model\Product\Attribute\Frontend\File',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => '',
                'used_in_product_listing' => false,
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'manual',
            [
                'group' => 'Information Sheet and Manual',
                'type' => 'varchar',
                'label' => 'Manual',
                'input' => 'file',
                'backend' => 'BL\FileAttributes\Model\Product\Attribute\Backend\File',
                'frontend' => 'BL\FileAttributes\Model\Product\Attribute\Frontend\File',
                'class' => '',
                'source' => '',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => '',
                'used_in_product_listing' => false,
            ]
        );
        /*Assign attribute to attribute set*/
        $entityType = $this->_resourceProduct->getEntityType();
        $attributeSetCollection = $this->_attributeSet->setEntityTypeFilter($entityType);
        foreach ($attributeSetCollection as $attributeSet) {
            $eavSetup->addAttributeToSet("catalog_product", $attributeSet->getAttributeSetName(), "General", "infosheet");
            $eavSetup->addAttributeToSet("catalog_product", $attributeSet->getAttributeSetName(), "General", "manual");
        }
    }
}
