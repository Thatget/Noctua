<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductCompatibility\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const COMP_EAV_TYPE = 'sit_productCompatibility_productcompatibility';

    const PRODUCT_TABLE = 'sit_productcompatibility_productcompatibility_product';

    const COMP_SOCKET = 'comp_socket';

    const COMP_MANUFACTURE = 'comp_manufacture';

    const COMP_MODEL = 'comp_model';

    const COMP_VALUE = 'comp_value';

    const COMP_TYPE = 'comp_type';

    const COMP_SERIES = 'comp_series';

    const COMP_MAINBOARD = 'Mainboard';

    const COMP_RAM = 'RAM';

    const COMP_CASE = 'Case';

    const COMP_CPU = 'CPU';

    const COMP_TYPE_COMPATIBLE = 'Compatible';

    const COMP_TYPE_INCOMPATIBLE = 'Incompatible';

    const COMP_TYPE_OC1 = 'OC1';

    const COMP_TYPE_OC2 = 'OC2';

    const COMP_TYPE_OC3 = 'OC3';

    const COMP_TYPE_PI = 'Possible Issues';
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $attributeOptionCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $entityAttribute;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * [__construct description]
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory [description]
     * @param \Magento\Eav\Model\Entity\Attribute                                        $entityAttribute                  [description]
     * @param \Magento\Store\Model\StoreManagerInterface                                 $storeManager                     [description]
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\ProductCompatibility\Model\ProductCompatibilityFactory $productCompFactory
    ) {
        $this->attributeOptionCollectionFactory = $attributeOptionCollectionFactory;
        $this->entityAttribute = $entityAttribute;
        $this->storeManager = $storeManager;
        $this->productCompFactory = $productCompFactory;
    }

    /**
     * Get all options name and value of the attribute
     *
     * @param int $attributeId
     * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    public function getAttributeOptionAll($attributeId)
    {
        return $this->attributeOptionCollectionFactory->create()
            ->setPositionOrder('asc')
            ->setAttributeFilter($attributeId)
            ->setStoreFilter()
            ->load();
    }

    /**
     * Get attribute info by attribute code and entity type
     *
     * @param mixed $entityType can be integer, string, or instance of class Mage\Eav\Model\Entity\Type
     * @param string $attributeCode
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttributeInfo($entityType, $attributeCode)
    {
        return $this->entityAttribute->loadByCode($entityType, $attributeCode);
    }

    /**
     * Get attribute option id
     *
     * @param  [type] $optionsArr [description]
     * @param  [type] $attrLabel  [description]
     * @return [type]             [description]
     */
    public function getAttrOptionId($optionsArr, $attrLabel)
    {
        $attrId = '';
        foreach ($optionsArr as $key => $value) {
            if ($value['value'] == $attrLabel) {
                $attrId = $value['option_id'];
            }
        }
        return $attrId;
    }

    /**
     * Get particular option's name and value of the attribute
     *
     * @param int $attributeId
     * @param int $optionId
     * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    public function getAttrOptionLabel($attributeId, $optionId)
    {
        return $this->attributeOptionCollectionFactory->create()
            ->setPositionOrder('asc')
            ->setAttributeFilter($attributeId)
            ->setIdFilter($optionId)
            ->setStoreFilter()
            ->load();
    }

    /**
     * Return product image pub media url
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getImage($folderPath, $imageName)
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $folderPath . $imageName;
    }

    /**
     * Return base url
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getCollectionSize($gridType, $productId)
    {
        $modelInfo = $this->getAttributeInfo(self::COMP_EAV_TYPE, self::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->getAttributeOptionAll($modelId);
        $optionId = $this->getAttrOptionId($modelAllOption, trim($gridType));
        $collection = $this->productCompFactory->create()->getCollection()->addAttributeToSelect('comp_type')->addAttributeToFilter('comp_type', ['eq' => $optionId]);

        $collection->getSelect()->joinLeft(
            ['productTable' => self::PRODUCT_TABLE],
            'e.entity_id = productTable.productcompatibility_id',
            ['productTable.product_id']
        )->where('productTable.product_id = ' . $productId)->group('e.entity_id');
        return $collection->getSize();
    }

    /**
     * Check related values which is entered in auto complete filter
     * @param  [type] $arrayData    [description]
     * @param  [type] $compareValue [description]
     * @return [type]               [description]
     */
    public function compareArrayValues($arrayData, $compareValue)
    {
        $optionIds = [];
        foreach ($arrayData as $key => $value) {
            if (stripos($value['default_value'], $compareValue) !== false) {
                $optionIds[] = $value['option_id'];
            }
        }
        return $optionIds;
    }
}
