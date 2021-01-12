<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\GeneralNewsCustom\Plugin\GeneralNews\Block\GeneralNews;
use Qsoft\GeneralNewsCustom\Setup\UpgradeData;

/**
 * Class GeneralNews
 *
 * @package Qsoft\GeneralNewsCustom\Plugin\GeneralNews\Block\GeneralNews
 */
class GeneralNews
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory
     */
    protected $_newscollFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * GeneralNews constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory $newscollFactory
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory $newscollFactory,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_storeManager = $storeManager;
        $this->_newscollFactory = $newscollFactory;
        $this->_request = $request;
    }

    /**
     * Update General News
     *
     * @param \SIT\GeneralNews\Block\GeneralNews\GeneralNews $subject
     * @param mixed $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetNewsDetails(
        \SIT\GeneralNews\Block\GeneralNews\GeneralNews $subject,
        $result
    ) {
        $currentStore = $this->_storeManager->getStore()->getId();
        $news_id = $this->_request->getParam('news_id');
        $newsData = $this->_newscollFactory->create();

        if ($currentStore != $this->getDefaultStoreId() &&
            (
                $result->getData(UpgradeData::USE_DEFAULT_NEWS_TITLE_CONFIG) ||
                $result->getData(UpgradeData::USE_DEFAULT_NEWS_DESCRIPTION_CONFIG) ||
                $result->getData(UpgradeData::USE_DEFAULT_NEWS_SHORT_DESCRIPTION_CONFIG)
            )
        ) {
            $defaultNewsColl = $newsData->setStoreId($this->getDefaultStoreId())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', ['eq' => 1])
                ->addFieldToFilter('entity_id', ['eq' => $news_id])
                ->getFirstItem();

            if ($result->getData(UpgradeData::USE_DEFAULT_NEWS_TITLE_CONFIG)) {
                $result->setNewsTitle($defaultNewsColl->getNewsTitle());
            }

            if ($result->getData(UpgradeData::USE_DEFAULT_NEWS_DESCRIPTION_CONFIG)) {
                $result->setNewsDesc($defaultNewsColl->getNewsDesc());
            }

            if ($result->getData(UpgradeData::USE_DEFAULT_NEWS_SHORT_DESCRIPTION_CONFIG)) {
                $result->setNewsShortdesc($defaultNewsColl->getNewsShortdesc());
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
