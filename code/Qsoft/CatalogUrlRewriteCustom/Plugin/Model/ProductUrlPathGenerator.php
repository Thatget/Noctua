<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qsoft\CatalogUrlRewriteCustom\Plugin\Model;

/**
 * Class ProductUrlPathGenerator
 *
 * @package Qsoft\CatalogUrlRewriteCustom\Plugin\Model
 */
class ProductUrlPathGenerator extends \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
{
    /**
     * Around getUrlPath function
     *
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $subject
     * @param callable $proceed
     * @param $product
     * @param $category
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetUrlPath(
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $subject,
        callable $proceed,
        $product,
        $category = null
    ) {
        $path = $product->getUrlKey()
            ? $this->prepareProductUrlKey($product)
            : $this->prepareProductDefaultUrlKey($product);

        return $category === null
            ? $path
            : $this->categoryUrlPathGenerator->getUrlPath($category) . '/' . $path;
    }
}
