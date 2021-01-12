<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace BL\FileAttributes\Block\Manual;

use SIT\MainAdmin\Helper\Data as MainAdminHelper;

class InstallationManual extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var MainAdminHelper
     */
    protected $sitHelper;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context               $context                  [description]
     * @param \Magento\Catalog\Model\CategoryFactory                         $categoryFactory          [description]
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager             [description]
     * @param \Magento\Framework\Registry                                    $registry                 [description]
     * @param MainAdminHelper                                                $sitHelper                [description]
     * @param array                                                          $data                     [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        MainAdminHelper $sitHelper,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->_registry = $registry;
        $this->sitHelper = $sitHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Manual Data for all stores
     */
    public function getManualData()
    {
        $categoryObj = $this->categoryFactory->create()->loadByAttribute('url_key', 'products');
        $subcategories = $categoryObj->getChildrenCategories();
        $currentStore = $this->storeManager->getStore()->getId();
        $stores = $this->getAllStores();
        $catName = [];

        foreach ($subcategories as $keySub => $subcategorie) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'manual']);
            $collection->addCategoryFilter($subcategorie);
            $collection->setStoreId($currentStore);
            $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $collection->setOrder('name', 'ASC');

            foreach ($collection as $key => $value) {
                if ($value->getManual() != '' || $value->getManual() != null) {
                    $catName[$subcategorie->getName()][$value->getName()] = [
                        'name' => $value->getName(),
                    ];
                }
            }
            uasort(
                $stores,
                function (\Magento\Store\Model\Store $storeA, \Magento\Store\Model\Store $storeB) {
                    return $storeA->getSortOrder() <=> $storeB->getSortOrder();
                }
            );
            foreach ($stores as $store) {
                $storeId = $store["store_id"];
                $storeCode = $store["code"];

                $collectionManual = $this->productCollectionFactory->create();
                $collectionManual->addAttributeToSelect(['name', 'manual']);
                $collectionManual->addCategoryFilter($subcategorie);
                $collectionManual->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                $collectionManual->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                $collectionManual->setStoreId($storeId);
                $collectionManual->setOrder('name', 'ASC');

                foreach ($collectionManual as $keyManu => $valueManu) {
                    if ($valueManu->getManual() != '' || $valueManu->getManual() != null) {
                        $catName[$subcategorie->getName()][$valueManu->getName()][$storeCode] = [
                            MainAdminHelper::MANUAL_VARIABLE => $valueManu->getManual(),
                        ];
                    }
                }
            }
        }
        return $catName;
    }

    /**
     * Get Manual and Infosheet Collection
     */
    public function getServiceManualSheetData($attr)
    {
        $currentProduct = $this->_registry->registry('current_product');
        $stores = $this->getAllStores();
        $proId = $currentProduct->getId();
        uasort(
            $stores,
            function (\Magento\Store\Model\Store $storeA, \Magento\Store\Model\Store $storeB) {
                return $storeA->getSortOrder() <=> $storeB->getSortOrder();
            }
        );
        $manualArr = [];
        foreach ($stores as $store) {
            $storeId = $store["store_id"];
            $storeCode = $store["code"];
            $collectionManual = $this->productCollectionFactory->create();
            $collectionManual->addAttributeToSelect($attr);
            $collectionManual->addFieldToFilter('entity_id', $proId);
            $collectionManual->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $collectionManual->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $collectionManual->setStoreId($storeId);
            foreach ($collectionManual as $keyManu => $valueManu) {
                if ($attr == MainAdminHelper::MANUAL_VARIABLE) {
                    if ($valueManu->getManual() != '' || $valueManu->getManual() != null) {
                        $manualArr[$storeCode] = [
                            MainAdminHelper::MANUAL_VARIABLE => $valueManu->getManual(),
                        ];
                    }
                }
                if ($attr == MainAdminHelper::INFOSHEET_VARIABLE) {
                    if ($valueManu->getInfosheet() != '' || $valueManu->getInfosheet() != null) {
                        $manualArr[$storeCode] = [
                            MainAdminHelper::INFOSHEET_VARIABLE => $valueManu->getInfosheet(),
                        ];
                    }
                }
            }
        }
        return $manualArr;
    }

    /**
     * Get All Stores
     */
    public function getAllStores()
    {
        return $this->storeManager->getStores();
    }
}
