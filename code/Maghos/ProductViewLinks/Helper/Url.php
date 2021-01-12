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
 * @copyright Copyright (c) 2018 Maghos s.r.o.
 * @license http://www.maghos.com/licenses/license-1.html
 * @author Magento dev team <support@maghos.eu>
 */
namespace Maghos\ProductViewLinks\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\StoreResolverInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;

/**
 * Class Url
 */
class Url extends AbstractHelper
{

    /** @var UrlInterface */
    private $frontendUrlModel;

    /** @var UrlFinderInterface */
    private $urlFinder;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param UrlInterface $frontendUrlModel
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        Context $context,
        UrlInterface $frontendUrlModel,
        UrlFinderInterface $urlFinder
    ) {
        $this->frontendUrlModel = $frontendUrlModel;
        $this->urlFinder        = $urlFinder;
        parent::__construct($context);
    }

    /**
     * Get product frontend URL
     *
     * @param int    $productId
     * @param int    $storeId
     * @param string $storeCode
     * @return string
     */
    public function getProductUrl($productId, $storeId, $storeCode)
    {
        $routeParams = [
            '_nosid'   => true,
            '_current' => false,
            '_query'   =>
                [
                    'viewlink'                         => 1,
                    StoreResolverInterface::PARAM_NAME => $storeCode
                ],
            'id' => $productId
        ];

        $this->frontendUrlModel->setScope($storeCode);

        $rewrites = $this->urlFinder->findAllByData([
            UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::ENTITY_ID => $productId,
            UrlRewrite::STORE_ID => $storeId,
        ]);

        if ($rewrites) {
            return $this->frontendUrlModel->getDirectUrl($rewrites[0]->getRequestPath(), $routeParams);
        }
        return $this->frontendUrlModel->getUrl('catalog/product/view', $routeParams);
    }
}
