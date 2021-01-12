<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ContentCustom\Block;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Cms block content block
 */
class LinkReplace extends \Magento\Framework\View\Element\Template
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Qsoft\ContentCustom\Model\ResourceModel\LinkReplace\CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var \Qsoft\ContentCustom\Helper\Data
     */
    protected $_helper;

    /**
     * LinkReplace constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Qsoft\ContentCustom\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Qsoft\ContentCustom\Helper\Data $helper,
        \Qsoft\ContentCustom\Model\ResourceModel\LinkReplace\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
    }
    public function getSettingStatus()
    {
        return $this->_helper->isFrontendEnableReplace();
    }
    public function getLinks()
    {
        $fullAction = $this->getRequest()->getRouteName() . '_' . $this->getRequest()->getControllerName() . '_' . $this->getRequest()->getActionName();
        $id = $this->getRequest()->getParam('id');
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
