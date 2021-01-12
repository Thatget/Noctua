<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Category;

use Magento\Framework\Controller\ResultFactory;

class Compproduct extends \Magento\Framework\App\Action\Action
{

    /**
     * sit product table name for join
     */
    const SIT_PRODUCTCOMP_PRODUCT_TABLE = 'sit_productcompatibility_productcompatibility_product';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory
     */
    protected $productCompFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context                                                $context            [description]
     * @param \Magento\Framework\View\Result\PageFactory                                           $resultPageFactory  [description]
     * @param \Magento\Catalog\Model\CategoryFactory                                               $categoryFactory    [description]
     * @param \Magento\Framework\Json\Helper\Data                                                  $jsonHelper         [description]
     * @param \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $productCompFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                                           $storeManager       [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $productCompFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryFactory = $categoryFactory;
        $this->jsonHelper = $jsonHelper;
        $this->productCompFactory = $productCompFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $categoryUrlKeys = ['cpu-cooler-retail', 'discontinued-products'];
        $allproductData = [];
        foreach ($categoryUrlKeys as $urlKey) {
            $productData = $this->getCategoryProducts($urlKey);
            if (count($productData) > 0) {
                array_push($allproductData, $productData);
            }
        }

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode($allproductData));
        return $response;
    }

    /**
     * Prepare array of product data
     * @param  [type] $categoryUrlKey [description]
     * @return [type]                 [description]
     */
    public function getCategoryProducts($categoryUrlKey)
    {
        $category_load = $this->categoryFactory->create()->loadByAttribute('url_key', $categoryUrlKey);
        $productData = [];
        $productCategoryData = [];
        $products = $category_load->getProductCollection()->addAttributeToSelect(['name'])
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToSort('name', 'ASC');
        $products->addUrlRewrite($category_load->getId());
        $products->addStoreFilter();
        $productData[] = ["category" => $category_load->getName()];
        $storeId = $this->storeManager->getStore()->getId();
        foreach ($products as $product) {
            $size = $this->getProductCompData($product->getId());
            if ($size > 0) {
                if ($this->getNccLink($product,$storeId) && $this->getNccLink($product,$storeId) != ''){
                    $link = $this->getNccLink($product,$storeId);
                    $target = '_blank';
                }else {
                    $link = $product->getProductUrl();
                    $target = '_self';
                }
                //$productData[] = ["name" => $product->getName(), "url_key" => $product->getProductUrl() . '/comp'];
                $productData[] = ["name" => $product->getName(), "url_key" => $link,"target"=> $target];
            }
        }

        return $productData;
    }

    public function getNccLink($product,$storeId)
    {
        $resourceProduct = $product->getResource();
        $link = "";
            if ($resourceProduct->getAttribute('general_link')) {
                $link = $resourceProduct->getAttributeRawValue($product->getId(), $resourceProduct->getAttribute('general_link'), $storeId);
                if ($link) return $link;
            }
        return '';
    }
    public function getProductCompData($productID)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $productComp = $this->productCompFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToSelect(['status'])
            ->addAttributeToFilter('status', ['eq' => 1]);
        /**
         * START : Join Query For Add Product Name : RH
         */
        $productComp->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTCOMP_PRODUCT_TABLE],
            'e.entity_id = t2.productcompatibility_id',
            ['t2.position', 't2.product_id']
        );

        $productComp->getSelect()->group('e.entity_id');
        $productComp->getSelect()->where('t2.product_id = ? ', $productID);
        /**
         * END : Join Query For Add Product Name : RH
         */
        return $productComp->getSize();
    }
}
