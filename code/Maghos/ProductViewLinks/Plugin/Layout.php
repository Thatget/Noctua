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
namespace Maghos\ProductViewLinks\Plugin;

/**
 * Class Layout
 */
class Layout
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
     * Check if layout is cacheable
     *
     * @param \Magento\Framework\View\Layout $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsCacheable(\Magento\Framework\View\Layout $subject, $result)
    {
        $disabled = $this->registry->registry('maghos_disable_layout_cache');
        return $result && !$disabled;
    }
}
