<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\FixUrls\Controller\Index;

use Magento\Framework\App\Action\Context;

/**
 * Class FixUrl
 *
 * @package Qsoft\FixUrls\Controller\Index
 */
class FixUrl extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * FixUrl constructor.
     *
     * @param Context $context
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory
     * @param \Magento\Store\Model\StoreManager $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        \Magento\Store\Model\StoreManager $storeManager
    ) {
        $this->pageFactory = $pageFactory;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Fix url rewrite
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        die("This is just a test controller");
        /*$collection = $this->pageFactory->create()->getCollection();
        $stores = $this->storeManager->getStores();
        try {
            // Remove duplicate url
            foreach ($collection as $cms) {
                // Get url by store id = 1
                $standardUrl = $this->urlRewriteFactory->create()->getCollection()
                    ->addFieldToFilter('request_path', $cms->getIdentifier())
                    ->addFieldToFilter('target_path', ['like' => '%cms/page/view/page_id/%'])
                    ->addFieldToFilter('store_id', 1)
                    ->getFirstItem()
                ;

                if ($standardUrl && $standardUrl->getId()) {
                    $urlColection = $this->urlRewriteFactory->create()->getCollection()
                        ->addFieldToFilter('request_path', $cms->getIdentifier())
                        ->addFieldToFilter('target_path', ['like' => '%cms/page/view/page_id/%'])
                        ->addFieldToFilter('store_id', ['gt' => 1]);

                    // remove wrong url rewrite (from other store id, store id > 1)
                    foreach ($urlColection as $url) {
                        if ($url->getEntityId() != $standardUrl->getEntityId()) {
                            $url->delete();
                        }
                    }

                    // Add new url for store id > 1
                    foreach ($stores as $store) {
                        if ($store->getId() > 1) {
                            // Check if existed url is correct
                            $urlColection = $this->urlRewriteFactory->create()->getCollection()
                                ->addFieldToFilter('request_path', $cms->getIdentifier())
                                ->addFieldToFilter('target_path', ['like' => '%cms/page/view/page_id/'.$standardUrl->getEntityId().'%'])
                                ->addFieldToFilter('store_id', $store->getId());

                            // if not existed
                            if (!$urlColection->getSize()) {
                                // Add new urls
                                $urlRewriteModel = $this->urlRewriteFactory->create();
                                try {
                                    $urlRewriteModel->setEntityType('cms-page')
                                        ->setEntityId($cms->getPageId())
                                        ->setRequestPath($cms->getIdentifier())
                                        ->setTargetPath('cms/page/view/page_id/' . $cms->getPageId())
                                        ->setStoreId($store->getId())
                                        ->save();
                                } catch (\Exception $e) {
                                    var_dump($e->getMessage());die("error");
                                }
                            }
                        }
                    }
                    $standardUrl->delete();
                }
            }
        }
        catch (\Exception $e) {
            var_dump($e->getMessage());
            die(__METHOD__);
        }*/
    }
}
