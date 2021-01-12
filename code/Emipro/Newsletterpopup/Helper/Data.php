<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [10-05-2019]
 */

namespace Emipro\Newsletterpopup\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Helper\Context                            $context                  [description]
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                       $storeManager             [description]
     * @param \Magento\Framework\App\Config\ScopeConfigInterface               $scopeConfig              [description]
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * [Get Country Collection]
     */
    public function getCountryList()
    {
        $countryCollection = $this->countryCollectionFactory->create()->toOptionArray(false);
        return $countryCollection;
    }

    /**
     * Get Store code
     */
    public function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * Get Site Key
     */
    public function getSiteKey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue("newsletter_popup/google_recaptcha/sitekey", $storeScope);
    }

    /**
     * Get Secret Key
     */
    public function getSecretKey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue("newsletter_popup/google_recaptcha/secretkey", $storeScope);
    }
}
