<?php

namespace SIT\CatalogSearch\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        Category $categoryCollection,
        StoreManagerInterface $storeManagerInterface,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        Http $request,
        array $data = []
    ) {
        $this->storeManagerInterface = $storeManagerInterface;
        $this->categoryCollection = $categoryCollection;
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    protected function _getProductCollection()
    {
        $searchword = htmlspecialchars( $this->getRequest()->getParam('q'),ENT_QUOTES, 'UTF-8');
        $searchword = explode(" ", $searchword);
        $proCollection = [];
        $categoryId = $this->storeManagerInterface->getStore()->getRootCategoryId();
        $category = $this->categoryCollection->load($categoryId);
        $productCollection = $category->getProductCollection()->addCategoryFilter($category)->addAttributeToSelect('*');
        $conditionArray = [];
        foreach ($searchword as $word) {
            $nameFilter = ["attribute" => "name", "like" => '%' . $word . '%'];
            array_push($conditionArray, $nameFilter);
            $featureFilter = ["attribute" => "feature_1_en", "like" => '%' . $word . '%'];
            array_push($conditionArray, $featureFilter);
        }
        $productCollection->addAttributeToFilter('visibility', ['in' => ['4']]);
        $productCollection->addAttributeToFilter('status', ['eq' => ['1']]);
        $productCollection->addAttributeToFilter($conditionArray);

        return $productCollection;
    }
}
