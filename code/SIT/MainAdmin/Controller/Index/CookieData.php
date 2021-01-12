<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SIT\MainAdmin\Controller\Index;

class CookieData extends \Magento\Framework\App\Action\Action
{
    protected $request;
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->request = $request;
        parent::__construct($context);
    }
    public function execute()
    {
        $param = $this->request->getParam('create');
        $onload = $this->request->getParam('onload');
        if ($onload == 'destroy') {
            if (isset($_COOKIE['menu'])) {
                setcookie("menu", "", time() - 3600, "/");
            }
            if (isset($_COOKIE['switcher'])) {
                setcookie('switcher', "", time() - 3600, "/");
            }
        } else {
            if ($param == 'openMenu') {
                setcookie('menu', 'opened', time() + (86400), "/"); // 86400 = 1 day
                if (isset($_COOKIE['switcher'])) {
                    setcookie('switcher', "", time() - 3600, "/");
                }
            } else {
                setcookie("menu", "", time() - 3600, "/");
                if (isset($_COOKIE['switcher'])) {
                    setcookie('switcher', "", time() - 3600, "/");
                }
            }
        }
    }
}
