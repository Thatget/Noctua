<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Responsivedesktop\Observer;

use \Magento\Framework\App\Cache\TypeListInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\View\DesignInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Theme\Model\Config;
use \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use \Magento\Theme\Model\ThemeFactory;

class LayoutBefore implements ObserverInterface
{

    /**
     * @var \Magento\Theme\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Theme\Model\ThemeFactory
     */
    protected $themeFactory;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * @param Config                                     $config            [description]
     * @param TypeListInterface                          $cacheTypeList     [description]
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool [description]
     * @param DesignInterface                            $design            [description]
     * @param StoreManagerInterface                      $storeManager      [description]
     * @param CollectionFactory                          $collectionFactory [description]
     * @param ThemeFactory                               $themeFactory      [description]
     */
    public function __construct(
        Config $config,
        TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        DesignInterface $design,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        ThemeFactory $themeFactory
    ) {
        $this->config = $config;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->cacheTypeList = $cacheTypeList;
        $this->design = $design;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->themeFactory = $themeFactory;
    }

    public function execute(Observer $observer)
    {
        // $this->cleanCache();
        if (isset($_COOKIE['view'])) {
            if ($_COOKIE['view'] === 'desktop') {
                $themes = $this->collectionFactory->create();
                foreach ($themes as $theme) {
                    if ($theme['theme_path'] === 'SIT/responsivedesktop') {
                        $this->design->setArea('frontend')
                            ->setDesignTheme('SIT/responsivedesktop');
                    }
                }
            }
        }
    }

    /**
     * To clean page cache
     */
    public function cleanCache()
    {
        $types = ['full_page'];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
