<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh
 */
namespace SIT\ProductCompatibility\Controller\Index;

class AttrCreate extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $_eavSetupFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute
     */
    protected $_attributeFactory;

    /**
     * @param \Magento\Framework\App\Action\Context                       $context           [description]
     * @param \Magento\Framework\View\Result\PageFactory                  $resultPageFactory [description]
     * @param \Magento\Eav\Setup\EavSetupFactory                          $eavSetupFactory   [description]
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager      [description]
     * @param \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory  [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_storeManager = $storeManager;
        $this->_attributeFactory = $attributeFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        // $attribute_arr = [
        //     '"Matisse"',
        //     '"Picasso"',
        // ];

        // $attributeInfo = $this->_attributeFactory->getCollection()
        //     ->addFieldToFilter('attribute_code', ['eq' => "comp_series"])
        //     ->getFirstItem();
        // $attribute_id = $attributeInfo->getAttributeId();
        // $option = array();
        // $option['attribute_id'] = $attributeInfo->getAttributeId();
        // foreach ($attribute_arr as $key => $value) {
        //     $option['value'][$value][0] = trim($value, '"');
        //     ONLY FOR STORE-WISE
        //     // foreach ($allStores as $store) {
        //     //     $option['value'][$value][$store->getId()] = $value;
        //     // }
        // }

        // $eavSetup = $this->_eavSetupFactory->create();
        // $eavSetup->addAttributeOption($option);
    }
}
