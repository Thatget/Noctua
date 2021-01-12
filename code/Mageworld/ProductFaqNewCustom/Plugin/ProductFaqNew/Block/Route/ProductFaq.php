<?php
/**
 * Copyright © Mageworld, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageworld\ProductFaqNewCustom\Plugin\ProductFaqNew\Block\Route;

/**
 * Class ProductFaq
 *
 * @package Mageworld\ProductFaqNewCustom\Plugin\ProductFaqNew\Block\Route
 */
class ProductFaq
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
     * ProductFaq constructor.
     *
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productFaqFactory = $productFaqFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Update Product FAQ
     * @param \SIT\ProductFaqNew\Block\Route\ProductFaq $subject
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $result
     * @param [type] $id [description]
     * @return \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $result
     */
    public function afterGetCustomRouteFAQ(
        \SIT\ProductFaqNew\Block\Route\ProductFaq $subject,
        $result,
        $id
    ) {
        $productFaq = $this->productFaqFactory->create();
        $storeId = $this->storeManager->getStore()->getId();
        if ($storeId != $this->getDefaultStoreId() &&
            (
                $result->getData('use_default_faq_que_config') ||
                $result->getData('use_default_faq_ans_config')
            )
        ) {
            $defaultProductFaqItem = $productFaq->setStoreId($this->getDefaultStoreId())
                ->addFieldToSelect('*')
                ->addFieldToFilter("entity_id", ["eq" => $id])
                ->getFirstItem();
            if ($result->getData('use_default_faq_que_config')) {
                $result->setFaqQue($defaultProductFaqItem->getFaqQue());
            }
            if ($result->getData('use_default_faq_ans_config')) {
                $result->setFaqAns($defaultProductFaqItem->getFaqAns());
            }
        }
        return $result;
    }

    /**
     * Get default store id
     *
     * @return int | mixed
     */
    public function getDefaultStoreId() {
        return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }
}
