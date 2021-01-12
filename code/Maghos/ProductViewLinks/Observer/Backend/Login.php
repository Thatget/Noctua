<?php
/**
 *
 * Maghos_ProductViewLinks Magento 2 extension
 *
 * NOTICE OF LICENSE
 *
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @category Maghos
 * @package Maghos_ProductViewLinks
 * @copyright Copyright (c) 2017 Maghos s.r.o.
 * @license http://www.maghos.com/licenses/license-1.html
 * @author Magento dev team <support@maghos.eu>
 */
namespace Maghos\ProductViewLinks\Observer\Backend;

/**
 * Class Login
 */
class Login implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * @var \Maghos\ProductViewLinks\Helper\Data
     */
    private $helper;

    /**
     * Login constructor.
     *
     * @param \Maghos\ProductViewLinks\Helper\Data $helper
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Maghos\ProductViewLinks\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->helper  = $helper;
        $this->session = $authSession;
    }

    /**
     * Set global acl cookie value
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $allowed = $this->session->isAllowed('Maghos_ProductViewLinks::show_disabled');
            $this->helper->setAllowed($allowed);
        } catch (\Exception $e) {
            return;
        }
    }
}
