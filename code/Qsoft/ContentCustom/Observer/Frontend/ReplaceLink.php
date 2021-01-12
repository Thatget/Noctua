<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qsoft\ContentCustom\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

class ReplaceLink implements ObserverInterface
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var FlagsSource
     */
    protected $helper;
    /**
     * @var \Qsoft\ContentCustom\Model\ResourceModel\LinkReplace\CollectionFactory
     */
    protected $_collectionFactory;
    protected $adminSession;
    /**
     * @param \Magento\Framework\Registry $registry
     * @param FlagsSource $flagsSource
     */
    public function __construct(
        \Qsoft\ContentCustom\Model\ResourceModel\LinkReplace\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Qsoft\ContentCustom\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $linksSearch = $this->helper->getReplaceLinksSearch();
        $linksReplace = $this->helper->getReplaceLinksReplace();
        $request = $observer->getEvent()->getRequest();
        $response = $observer->getEvent()->getResponse();
        //$linksSearch = ['tuanhatay'];
        $fullActionName = $request->getRouteName() . '_' . $request->getControllerName() . '_' . $request->getActionName();
        if($request->getControllerName() == 'system_config' || in_array($fullActionName, $this->helper->getExcludeFullAction())
        )
        {
            return $this;
        }
        if ($linksSearch && $linksReplace && $this->helper->isFrontendEnableReplace()) {
            $html = $response->getContent();
            $linkReplace = $linksReplace[0];
            foreach ($linksSearch as $key => $link)
            {
                $link = trim($link);
                if(isset($linksReplace[$key]))
                {
                    $linkReplace = $linksReplace[$key];
                }
                $html = str_replace($link, $linkReplace, $html);
            }
            $response->setContent($html);
        }
        $localeCode =  $this->helper->getLocaleCode($this->_storeManager->getStore()->getId());
        $countryCode = explode('_', $localeCode);
        $countryCode = $countryCode[0];
        $currCountryCode = '';
        if(isset($_SERVER['GEOIP_COUNTRY_CODE']))
        {
            $currCountryCode = $_SERVER['GEOIP_COUNTRY_CODE'];
        }
        if($this->helper->isFrontendEnableReplace() && (!$currCountryCode || $currCountryCode == $countryCode))
        {
            $html = $response->getContent();
            foreach ($this->getLinks($request) as $link)
            {
                $html = str_replace($link['link_search'], $link['link_replace'], $html);
            }
            $response->setContent($html);
        }
        return $this;
    }
    public function getLinks($request)
    {
        $fullAction = $request->getRouteName() . '_' . $request->getControllerName() . '_' . $request->getActionName();
        $id = $request->getParam('id');
        $links = [];
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('full_action_name', $fullAction)->addFieldToFilter('object_id', $id)->addStoreFilter($this->_storeManager->getStore());
        foreach ($collection as $link)
        {
            $links[] = ['link_replace' => $link->getLinkReplace(), 'link_search' => $link->getLinkSearch()];
        }
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('full_action_name', $fullAction)
            ->addFieldToFilter('object_id', [['null' => true], ['eq'=> '']])->addStoreFilter($this->_storeManager->getStore());
        foreach ($collection as $link)
        {
            $links[] = ['link_replace' => $link->getLinkReplace(), 'link_search' => $link->getLinkSearch()];
        }
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('full_action_name', [['null' => true], ['eq'=> '']])
            ->addFieldToFilter('object_id', [['null' => true], ['eq'=> '']])->addStoreFilter($this->_storeManager->getStore());
        foreach ($collection as $link)
        {
            $links[] = ['link_replace' => $link->getLinkReplace(), 'link_search' => $link->getLinkSearch()];
        }
        return $links;
    }
}
