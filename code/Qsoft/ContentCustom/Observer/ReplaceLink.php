<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Qsoft\ContentCustom\Observer;

use Magento\Framework\Event\ObserverInterface;

class ReplaceLink implements ObserverInterface
{

    /**
     * @var FlagsSource
     */
    protected $helper;
    protected $adminSession;
    /**
     * @param \Magento\Framework\Registry $registry
     * @param FlagsSource $flagsSource
     */
    public function __construct(
        \Qsoft\ContentCustom\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->helper = $helper;
        $this->adminSession = $authSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $user = $this->adminSession->getUser();
        $fullActionName = $request->getRouteName() . '_' . $request->getControllerName() . '_' . $request->getActionName();
        if($request->getControllerName() == 'system_config' || !$user
            || in_array($fullActionName, $this->helper->getExcludeFullAction())
        )
        {
            return $this;
        }
        $linksSearch = $this->helper->getReplaceLinksSearch();
        $linksReplace = $this->helper->getReplaceLinksReplace();
        $userRoles = $this->helper->getUserRolesReplace();
        $users = $this->helper->getUsersReplace();
        $userRole = $user->getRole();
        //$linksSearch = ['tuanhatay'];
        $checkUser = true;
        if($userRoles && !in_array($userRole->getId(), $userRoles))
        {
            $checkUser = false;
        }
        if($users && !in_array($user->getId(), $users))
        {
            $checkUser = false;
        }
        if ($linksSearch && $linksReplace && $checkUser) {
            $response = $observer->getEvent()->getResponse();
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
        return $this;
    }
}
