<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qsoft\ContentCustom\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * General
     */
    const XML_PATH_GENERAL_REPLACE_LINKS_SEARCH = 'admin/content_custom/links_search';

    const XML_PATH_GENERAL_REPLACE_LINKS_REPLACE = 'admin/content_custom/links_replace';
    const XML_PATH_GENERAL_REPLACE_USERS = 'admin/content_custom/users';
    const XML_PATH_GENERAL_REPLACE_ROLES = 'admin/content_custom/roles';
    const XML_PATH_GENERAL_REPLACE_ENABLE_FRONTEND = 'admin/content_custom/enable_frontend';
    const XML_PATH_GENERAL_REPLACE_EXCLUDE_FULL_ACTION = 'admin/content_custom/exclude_fullaction';
    const XML_PATH_GENERAL_REPLACE_FULL_ACTION_LIST = 'admin/content_custom/fullaction_list';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return array|false|string[]
     */
    public function getReplaceLinksSearch()
    {
        $links = [];
        if($linksSetting = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REPLACE_LINKS_SEARCH))
        {
            $links = explode(',', $linksSetting);
        }
        return $links;
    }

    /**
     * @return array|false|string[]
     */
    public function getReplaceLinksReplace()
    {
        $links = [];
        if($linksSetting = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REPLACE_LINKS_REPLACE))
        {
            $links = explode(',', $linksSetting);
        }
        return $links;
    }
    public function getUserRolesReplace()
    {
        $result = [];
        if($setting = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REPLACE_ROLES))
        {
            $result = explode(',', $setting);
        }
        return $result;
    }
    public function getUsersReplace()
    {
        $result = [];
        if($setting = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REPLACE_USERS))
        {
            $result = explode(',', $setting);
        }
        return $result;
    }
    public function isFrontendEnableReplace()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REPLACE_ENABLE_FRONTEND,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }
    public function getExcludeFullAction()
    {
        $result = [];
        if($setting = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REPLACE_EXCLUDE_FULL_ACTION))
        {
            $result = explode(',', str_replace([' ', "\n"], '', $setting));
        }
        return $result;
    }
    public function getFullActions()
    {
        $result = [];
        if($setting = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REPLACE_FULL_ACTION_LIST))
        {
            $result = explode(',', str_replace([' ', "\n"], '', $setting));
        }
        return $result;
    }
    public function getLocaleCode($storeId)
    {
        return $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
