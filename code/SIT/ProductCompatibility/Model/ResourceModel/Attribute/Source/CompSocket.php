<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductCompatibility\Model\ResourceModel\Attribute\Source;

class CompSocket extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * [__construct description]
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory [description]
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory            $attrOptionFactory           [description]
     * @param \Magento\Eav\Model\Config                                                  $eavConfig                   [description]
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        /*$attribute = $this->eavConfig->getAttribute(ProductCompatibilitySetup::ENTITY_TYPE_CODE, 'comp_socket');
    return $attribute->getSource()->getAllOptions();*/
    }

    public function getOptionsArray($withEmpty = true)
    {
        /* $options = [];
    foreach ($this->getAllOptions($withEmpty) as $option) {
    $options[$option['value']] = $option['label'];
    }
    return $options;*/
    }
}
