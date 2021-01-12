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
namespace Maghos\ProductViewLinks\Helper;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const COOKIE_NAME = 'maghos_product_view';

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $manager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $metadataFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $metadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $metadataFactory
    ) {
        parent::__construct($context);
        $this->manager = $cookieManager;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Set allowed flag
     *
     * @param boolean $value
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setAllowed($value)
    {
        $cookieMetadata = $this->metadataFactory->createSensitiveCookieMetadata()
                                                ->setPath('/');
        $this->manager->setSensitiveCookie(
            \Maghos\ProductViewLinks\Helper\Data::COOKIE_NAME,
            (int)$value,
            $cookieMetadata
        );
    }

    /**
     * Check if access is allowed.
     *
     * @return bool
     */
    public function isAllowed()
    {
        return (bool)$this->manager->getCookie(\Maghos\ProductViewLinks\Helper\Data::COOKIE_NAME, 0);
    }
}
