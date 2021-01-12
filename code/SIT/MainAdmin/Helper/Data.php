<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\MainAdmin\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const INFOSHEET_VARIABLE = 'infosheet';

    const MANUAL_VARIABLE = 'manual';

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var  \Magento\Framework\Json\Encoder
     */
    protected $jsonEncoder;

    /**
     * @var  \Magento\Cms\Model\PageFactory
     */
    protected $cmsPageFactory;

    /**
     * @var  \Magento\Variable\Model\VariableFactory
     */
    protected $varFactory;

    /**
     * @param \Magento\Catalog\Helper\Category           $categoryHelper  [description]
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider  [description]
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager    [description]
     * @param \Magento\Framework\Registry                $registry        [description]
     * @param \Magento\Catalog\Model\CategoryFactory     $categoryFactory [description]
     * @param \Magento\Catalog\Model\ProductFactory      $productFactory  [description]
     * @param \Magento\Framework\Json\Encoder            $jsonEncoder     [description]
     * @param \Magento\Cms\Model\PageFactory             $cmsPageFactory  [description]
     * @param \Magento\Variable\Model\VariableFactory    $varFactory      [description]
     */
    public function __construct(
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Json\Encoder $jsonEncoder,
        \Magento\Cms\Model\PageFactory $cmsPageFactory,
        \Magento\Variable\Model\VariableFactory $varFactory
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->filterProvider = $filterProvider;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->jsonEncoder = $jsonEncoder;
        $this->cmsPageFactory = $cmsPageFactory;
        $this->varFactory = $varFactory;
    }

    /**
     * For get product by id
     *
     * @return \Magento\Catalog\Model\ProductFactory
     */
    public function getProductData($proId)
    {
        return $this->productFactory->create()->load($proId);
    }

    /**
     * For get category url
     *
     * @return \Magento\Catalog\Helper\Category
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    /**
     * For get first level category data
     *
     * @return \Magento\Catalog\Helper\Category
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }

    /**
     * For get Child Categories data
     *
     * @return \Magento\Catalog\Helper\Category
     */
    public function getChildCategories($category)
    {
        $subcategories = $category->getChildren();
        return $subcategories;
    }

    /**
     * Return product name list using category url key
     *
     * @return \Magento\Framework\Json\Encoder
     */
    public function getProductNameList($catUrlKey, $fullActionName = '')
    {
        $productArray = [];
        if ($fullActionName == 'catalogsearch_result_index') {
            $product = $catUrlKey;
        } else {
            $product = $this->categoryFactory->create()->loadByAttribute('url_key', $catUrlKey)->getProductCollection()->addAttributeToSelect('*');
            $product->addAttributeToSort('position');
            $product->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $product->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $product->addAttributeToSort('position', 'ASC');
        }
        foreach ($product as $key => $value) {
            $productArray[] = ['id' => $value->getId(), 'name' => $value->getName()];
        }
        return $this->jsonEncoder->encode($productArray);
    }

    public function getProductByCategory($catUrlKey)
    {
        $product = $this->categoryFactory->create()->loadByAttribute('url_key', $catUrlKey)->getProductCollection()->addAttributeToSelect(['entity_id', 'sku']);
        $product->addAttributeToSort('position', 'ASC');
        $product->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $product->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        return $product;
    }

    /**
     * Return product ids list using category url key
     *
     * @return \Magento\Framework\Json\Encoder
     */
    public function getProductIdsList($catUrlKey, $fullActionName = '')
    {
        $productArray = [];
        if ($fullActionName == 'catalogsearch_result_index') {
            $product = $catUrlKey;
        } else {
            $product = $this->categoryFactory->create()->loadByAttribute('url_key', $catUrlKey)->getProductCollection()->addAttributeToSelect('*');
            $product->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $product->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $product->addAttributeToSort('position', 'ASC');
        }
        $count = 0;
        foreach ($product as $key => $value) {
            $productArray[$count] = $value->getId();
            $count++;
        }
        return $this->jsonEncoder->encode($productArray);
    }
    /**
     * Return base url
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getBaseUrl()
    {
        return $this->jsonEncoder->encode($this->storeManager->getStore()->getBaseUrl());
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

    /**
     * Return current category data
     *
     * @return \Magento\Framework\Registry
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * Return current product data
     *
     * @return \Magento\Framework\Registry
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Return filter html data
     *
     * @return \Magento\Cms\Model\Template\FilterProvider
     */
    public function getCmsFilterContent($contentData)
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->filterProvider->getBlockFilter()->setStoreId($storeId)->filter($contentData);
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
     * [getPubMedia description]
     * @return [type] [description]
     */
    public function getPubMedia()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Return custom variable
     *
     * @return \Magento\Variable\Model\VariableFactory
     */
    public function getVariableValue($varCode)
    {
        $var = $this->varFactory->create();
        $var->loadByCode($varCode);
        return $var->getValue('text');
    }

    /**
     * Return cms page data by identifier
     *
     * @return \Magento\Cms\Model\PageFactory
     */
    public function getCmsPageData($cmsIdentifier)
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->cmsPageFactory->create()->setStoreId($storeId)->load($cmsIdentifier, 'identifier');
    }

    public function getFlagCode($flagName)
    {
        $flagGif = '';
        $langFlags = [
            'Arabic' => 'sa.gif',
            'Bosnian' => 'ba.gif',
            'Bulgarian' => 'bg.gif',
            'Chinese' => 'cn.gif',
            'Croatian' => 'hr.gif',
            'Czech' => 'cz.gif',
            'Danish' => 'dk.gif',
            'Dutch' => 'nl.gif',
            'English' => 'gb.gif',
            'Estonian' => 'ee.gif',
            'Finnish' => 'fi.gif',
            'French' => 'fr.gif',
            'German' => 'de.gif',
            'Greek' => 'gr.gif',
            'Hebrew' => 'il.gif',
            'Hungarian' => 'hu.gif',
            'Indonesian' => 'id.gif',
            'Italian' => 'it.gif',
            'Japanese' => 'jp.gif',
            'Korean' => 'kr.gif',
            'Latvian' => 'lv.gif',
            'Lithuanian' => 'lt.gif',
            'Montenegrin' => 'me.gif',
            'Norwegian' => 'no.gif',
            'Persian' => 'ir.gif',
            'Polish' => 'pl.gif',
            'Portuguese' => 'pt.gif',
            'Romanian' => 'ro.gif',
            'Russian' => 'ru.gif',
            'Serbian' => 'rs.gif',
            'Slovak' => 'sk.gif',
            'Slovenian' => 'si.gif',
            'Spanish' => 'es.gif',
            'Swedish' => 'se.gif',
            'Thai' => 'th.gif',
            'Turkish' => 'tr.gif',
            'Ukrainian' => 'ua.gif',
        ];
        if (isset($langFlags[$flagName])) {
            $flagGif = $langFlags[$flagName];
        }
        return $flagGif;
    }
    public function getLocaleCode($storeId)
    {
        return $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
