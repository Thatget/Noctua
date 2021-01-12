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
namespace Maghos\ProductViewLinks\Plugin\Catalog\Helper\Product;

/**
 * Class View
 */
class View
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Product constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Execute controller action
     *
     * @param \Magento\Catalog\Controller\Product\View $subject
     * @param \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect $result
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function afterExecute(
        \Magento\Catalog\Controller\Product\View $subject,
        $result
    ) {
        $isLink = $subject->getRequest()->getParam('viewlink', 0);
        if ($isLink) {
            $result->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        }

        return $result;
    }

    /**
     * Execute controller action
     *
     * @param \Magento\Catalog\Controller\Product\View $subject
     * @return array
     */
    public function beforeExecute(
        \Magento\Catalog\Controller\Product\View $subject
    ) {
        $isLink = $subject->getRequest()->getParam('viewlink', 0);
        if ($isLink) {
            $this->registry->register('maghos_disable_layout_cache', true);
        }

        return [];
    }
}
