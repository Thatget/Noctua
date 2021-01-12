<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SIT\CatalogSearch\Controller\Ajax;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Store\Model\StoreManagerInterface;

class Suggest extends Action implements HttpGetActionInterface
{
    protected $resultJsonFactory;
    protected $suggestData = null;

    /**
     * @var  \Magento\Search\Model\AutocompleteInterface
     */
    private $autocomplete;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Search\Model\AutocompleteInterface $autocomplete
     */
    public function __construct(
        Context $context,
        Category $categoryCollection,
        StoreManagerInterface $storeManagerInterface,
        AutocompleteInterface $autocomplete,
        \Magento\Catalog\Model\ProductFactory $ProductFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->categoryCollection = $categoryCollection;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->productFactory = $ProductFactory;
        $this->autocomplete = $autocomplete;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }
        $html = '<div style="font-weight:bold;padding:7px;text-transform: uppercase">' . __('Products') . '</div>';
        $suggestData = $this->getSuggestData();
        $notfound = [];
        $product_count = 0;
        $html_other = '';
        $other_text = '';
        $html_product = '';
        $html_other .= '<li class="head">' . __('Other Search Results') . '</li>';
        foreach ($suggestData as $index) {
            if (isset($index['product'])) {
                foreach ($index['product'] as $single_row) {
                    $html_product .= '<li title="' . html_entity_decode($single_row['title']) . '" class="prod ' . $single_row['row_class'] . '">
                        <a href="' . $single_row["url"] . '" style="display: inline-block; height: 100%; width: 100%;">' . html_entity_decode($single_row["title"]) . '
                        </a></li>';
                    $product_count++;
                    if (!in_array("product", $notfound)) {
                        array_push($notfound, "product");
                    }
                }
            }
            if (isset($index["other"])) {
                $other_text .= "<li title='" . html_entity_decode($index['other']['title']) . "' class='prod " . $index['other']['row_class'] . "'>
                    <a href='" . $index['other']['url'] . "' class='other-link' style='display: inline-block; height: 100%; width: 100%;'>" . html_entity_decode($index['other']['title']) . "
                    </a></li>";
            }
        }
        if (!in_array("product", $notfound)) {
            $html_product = '<ul><li>' . __('Your search returns no results.') . '</li></ul>';
        }
        $html_other .= $other_text;
        $html_compatibility_data = '<li class="head">' . __('COMPATIBILITY') . '</li>';
        $newPage = 'https://ncc.noctua.at/?hq=';
        $html_compatibility_data .= "<li class='prod'>
            <a target='_blank' href='". /* @escapeNotVerified */ ($newPage.$this->getRequest()->getParam('q'))."' class='other-link' style='display: inline-block; height: 100%; width: 100%;'>Click here to search our compatibility database
            </a></li>";
        $final_html_product = '<ul><li class="head">' . __('Products') . ' (' . $product_count . ')</li>';
        $final_html_product .= $html_product;
        $final_html_product .= $html_other .$html_compatibility_data. '</ul>';
        $result = $this->resultJsonFactory->create();
        $result->setData($final_html_product);
        return $result;
    }

    public function getSuggestData()
    {
        if (!$this->suggestData) {
            $final_result = [];
            $searchText = '';
            $counter = 0;
            $query = htmlspecialchars( $this->getRequest()->getParam('q'),ENT_QUOTES, 'UTF-8');
            $isQuoted = false;
            $text = trim($query);
            $len = strlen($text);
            $data_all_products = [];
            $data_discontinued_prods = [];
            $baseUrl = $this->storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            $storeCode = $this->storeManagerInterface->getStore()->getCode();
            $storeId = $this->storeManagerInterface->getStore()->getStoreId();
            if ($len >= 2) {
                if (strpos($text, '"') !== false && strpos($text, '"') == 0 && strrpos($text, '"') !== false && strrpos($text, '"') == ($len - 1)) {
                    $isQuoted = true;
                    $query = trim($query);
                }
                $searchText = $query;
                $collection = $this->productFactory->create()->setStoreId($storeId)->getCollection()->addAttributeToSelect('*');
                $collection->addStoreFilter($this->storeManagerInterface->getStore());
                $searchword = explode(" ", $query);
                $conditionArray = [];
                foreach ($searchword as $word) {
                    $nameFilter = ["attribute" => "name", "like" => '%' . $word . '%'];
                    array_push($conditionArray, $nameFilter);
                    $featureFilter = ["attribute" => "feature_1_en", "like" => '%' . $word . '%'];
                    array_push($conditionArray, $featureFilter);
                }
                $collection->addAttributeToFilter('visibility', ['in' => ['4']]);
                $collection->addAttributeToFilter('status', ['eq' => ['1']]);
                $collection->addAttributeToFilter($conditionArray);
                $collection->addAttributeToSort('name', 'asc');
                $ids_discontinued_prods = [];

                $discontinuedCatId = $this->categoryCollection->loadByAttribute('url_key', 'discontinued-products')->getId();
                $category_discontinued_prods = $this->categoryCollection->load($discontinuedCatId);
                $ids_discontinued_prods = $category_discontinued_prods->getProductCollection()->addCategoryFilter($category_discontinued_prods)->addAttributeToSelect('*')->getAllIds();
                $temp = 0;
                foreach ($collection as $c) {
                    $path = $c->getUrlKey();
                    $url = $baseUrl . $storeCode . "/" . $path;
                    $_data = [
                        'title' => $c->getName(),
                        'row_class' => ($counter++) % 2 ? 'odd' : 'even',
                        'num_of_results' => 1,
                        'url' => $url,
                    ];
                    if (in_array($c->getId(), $ids_discontinued_prods)) {
                        $data_discontinued_prods[] = $_data;
                        $temp++;
                    } else {
                        $data_all_products[] = $_data;
                    }
                }
                if (count($data_all_products) > 0 || count($data_discontinued_prods) > 0) {
                    $final_result[] = ["product" => $data_all_products];
                    $final_result[] = ["product" => $data_discontinued_prods];
                    $data_all_products = [];
                    $_data = [];
                    $data_discontinued_prods = [];
                }
            }
            if ($isQuoted) {
                $final_result[] = ["other" => [
                    'title' => $searchText,
                    'row_class' => ($counter++) % 2 ? 'odd' : 'even',
                    'num_of_results' => 1,
                    'url' => $baseUrl . $storeCode . '/catalogsearch/result/?q="' . trim($searchText, '"') . '"',
                ]];
            } else {
                $final_result[] = ["other" => [
                    'title' => $searchText,
                    'row_class' => ($counter++) % 2 ? 'odd' : 'even',
                    'num_of_results' => 1,
                    'url' => $baseUrl . $storeCode . '/catalogsearch/result/?q=' . $searchText . '',
                ]];
            }
            $this->suggestData = $final_result;
        }

        return $this->suggestData;
    }
}
