<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh
 */
namespace SIT\ProductFaqNew\Block\ProductFaq;

class FaqQuestions extends \Magento\Framework\View\Element\Template
{
    /**
     * sit product table name for join product name
     */
    const SIT_PRODUCTFAQ_PRODUCT_TABLE = 'sit_productfaqnew_productfaq_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory
     */
    protected $faqCategoryFactory;

    /**
     *  \Magento\Catalog\Model\ProductFactory
     */
    protected $product;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    protected $counter = 0;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context                    $context            [description]
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager       [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory  [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory   $faqCategoryFactory [description]
     * @param \Magento\Catalog\Model\ProductFactory                               $product            [description]
     * @param \Magento\Framework\App\Request\Http                                 $request            [description]
     * @param array                                                               $data               [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory $faqCategoryFactory,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->productFaqFactory = $productFaqFactory;
        $this->faqCategoryFactory = $faqCategoryFactory;
        $this->product = $product;
        $this->request = $request;
        parent::__construct($context, $data);
    }

    public function getFAQCategoryTree()
    {
        $cat_obj = $this->faqCategoryFactory->create()->addFieldToSelect('*')->addFieldToFilter('status', ['eq' => 1])->setOrder('position', 'ASC');
        return $cat_obj;
    }

    public function getFAQSearchList()
    {
        /* Get FAQs collection */
        $storeId = $this->storeManager->getStore()->getId();
        $cat_obj = $this->productFaqFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter('status', ['eq' => 1])->addFieldToFilter("category_id", ["neq" => '']);
        $questionList = [];
        $counter = 0;
        foreach ($cat_obj as $key => $value) {
            $questionList[$counter]['category'] = "FAQs";
            $questionList[$counter]['value'] = $value->getId();
            $questionList[$counter]['label'] = $value->getFaqQue() . 'ANS:' . $value->getFaqAns();
            $counter++;
        }

        /* Get custom category collection */
        $customCat = $this->faqCategoryFactory->create()->addFieldToSelect(['id', 'cat_name'])->addFieldToFilter('status', ['eq' => 1])->setOrder('position', 'ASC');
        $categoryList = [];
        $counterCat = 0;

        foreach ($customCat as $key => $value) {
            $categoryList[$counterCat]['category'] = "Categories";
            $categoryList[$counterCat]['value'] = $value->getId();
            $categoryList[$counterCat]['label'] = $value->getCatName();
            $counterCat++;
        }

        /* Get product collection */
        $storeId = $this->storeManager->getStore()->getId();
        $faqs = $this->productFaqFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1]);

        $faqs->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTFAQ_PRODUCT_TABLE],
            'e.entity_id = t2.productfaq_id',
            ['t2.product_id']
        );
        $faqs->getSelect()->where('t2.product_id != ?', '');
        $faqs->getSelect()->group('t2.product_id');
        $faqs->getSelect()->order(['position ASC', 'created_at DESC']);

        $productList = [];
        $counterPro = 0;

        foreach ($faqs->getData() as $keyData => $valuePro) {
            $productList[]['category'] = "Products";
            $productList[$counterPro]['value'] = $valuePro['product_id'];
            $proName = $this->product->create()->load($valuePro['product_id']);
            $productList[$counterPro]['label'] = $proName->getName();
            $counterPro++;
        }

        return json_encode(array_merge($questionList, $categoryList, $productList));
    }

    public function arrayTree(array $elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($parentId == '0') {
                $this->counter = 0;
                $element['level'] = 0;
                $this->counter++;
            }
            if ($element['parent_cat_name'] == $parentId) {
                $children = $this->arrayTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                    $element['level'] = $this->counter;
                    $this->counter++;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function generateTree(array $data, $flag = 0, $parentId = 0)
    {
        $result = [];
        if (is_array($data) && count($data) > 0) {
            if ($flag == 1) {
                $result[] = '<ul class="sub">';
            }
            foreach ($data as $entry) {
                if (isset($entry['children'])) {
                    // $class_name = ($entry['parent_cat_name'] == "0" ? "active-collapse" : 'deactive-collapse');
                    $result[] = sprintf('<li class="collapse" data-parentcategory=' . $entry['parent_cat_name'] . ' data-id= ' . $entry['id'] . '><div data-id=' . $entry['id'] . '><span class="sit-plus-icon"></span><a class="li-child-text" data-id=' . $entry['id'] . '>%s</a></div>%s</li>', $entry['cat_name'], $this->generateTree($entry['children'], 1));
                } else {
                    // $class_name = ($entry['parent_cat_name'] == "0" ? "active-collapse" : 'deactive-collapse');
                    $result[] = sprintf('<li class="single collapse" data-parentcategory=' . $entry['parent_cat_name'] . ' data-id=' . $entry['id'] . '><div data-id=' . $entry['id'] . '><a class="li-single-text" data-id=' . $entry['id'] . '>%s</a></div></li>', $entry['cat_name']);
                }
            }
            if ($flag == 1) {
                $result[] = '</ul>';
            }
        }
        return implode($result);
    }

    public function getFAQList()
    {
        $product_id = $this->request->getParam('product_id');
        $category_id = $this->request->getParam('category_id');
        $faq_search_id = $this->request->getParam('queid');
        $faq_search_name = $this->request->getParam('quename');
        $productfaqnew = [];

        if ($product_id) {
            $storeId = $this->storeManager->getStore()->getId();
            $productfaqnew = $this->productFaqFactory->create()
                ->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', ['eq' => 1]);

            $productfaqnew->getSelect()->joinLeft(
                ['t2' => self::SIT_PRODUCTFAQ_PRODUCT_TABLE],
                'e.entity_id = t2.productfaq_id',
                ['t2.product_id']
            );
            $productfaqnew->getSelect()->where('t2.product_id = ?', $product_id);
        }
        if ($category_id) {
            $storeId = $this->storeManager->getStore()->getId();
            $productfaqnew = $this->productFaqFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("category_id", ["finset" => $category_id]);
        }
        if ($faq_search_id) {
            $storeId = $this->storeManager->getStore()->getId();
            $productfaqnew = $this->productFaqFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $faq_search_id]);
        }
        if ($faq_search_name) {
            $storeId = $this->storeManager->getStore()->getId();
            $productfaqnew = $this->productFaqFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("faq_que", ["like" => '%' . $faq_search_name . '%'])->addFieldToFilter("category_id", ["neq" => '']);
        }
        if ($product_id == "" && $category_id == "" && $faq_search_id == "" && $faq_search_name == "") {
            $storeId = $this->storeManager->getStore()->getId();
            $productfaqnew = $this->productFaqFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("category_id", ["neq" => '']);
        }
        return $productfaqnew;
    }
}
