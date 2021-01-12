<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh
 */
namespace SIT\ProductFaqNew\Block\Route;

class ProductFaq extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context                    $context           [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager      [description]
     * @param array                                                               $data              [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->productFaqFactory = $productFaqFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Return custom router data of product faq based on id
     *
     * @param  [type] $id [description]
     * @return \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    public function getCustomRouteFAQ($id)
    {
        $productFaq = $this->productFaqFactory->create();
        $storeId = $this->storeManager->getStore()->getId();
        $productFaqItem = $productFaq->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
        return $productFaqItem;
    }
}
