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
namespace Maghos\ProductViewLinks\Plugin\Catalog\Helper;

/**
 * Class Product
 */
class Product
{

    /**
     * @var bool
     */
    private $overrideShow = false;

    /**
     * @var \Maghos\ProductViewLinks\Helper\Data
     */
    private $helper;

    /**
     * Product constructor.
     *
     * @param \Maghos\ProductViewLinks\Helper\Data $helper
     */
    public function __construct(\Maghos\ProductViewLinks\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Init product to be used for product controller actions and layouts
     *
     * @param \Magento\Catalog\Helper\Product $subject
     * @param \Closure $proceed
     * @param $productId
     * @param \Magento\Framework\App\Action\Action $controller
     * @param null $params
     * @return bool|\Magento\Catalog\Helper\Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInitProduct(
        \Magento\Catalog\Helper\Product $subject,
        \Closure $proceed,
        $productId,
        \Magento\Framework\App\Action\Action $controller,
        $params = null
    ) {
        $isLink = $controller->getRequest()->getParam('viewlink', 0);
        if ($isLink && $this->helper->isAllowed()) {
            $this->overrideShow = true;
        }

        $product = $proceed($productId, $params);

        $this->overrideShow = false;

        return $product;
    }

    /**
     * Check if a product can be shown
     *
     * @param \Magento\Catalog\Helper\Product $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product|int $product
     * @param string $where
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCanShow(
        \Magento\Catalog\Helper\Product $subject,
        \Closure $proceed,
        $product,
        $where = 'catalog'
    ) {
        if ($this->overrideShow) {
            return true;
        }

        return $proceed($product, $where);
    }
}
