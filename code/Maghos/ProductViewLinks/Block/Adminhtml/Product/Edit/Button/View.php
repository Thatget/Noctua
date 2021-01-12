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
namespace Maghos\ProductViewLinks\Block\Adminhtml\Product\Edit\Button;

use Magento\Ui\Component\Control\Container;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Store\Model\StoreManagerInterface;
use Maghos\ProductViewLinks\Helper\Url as UrlHelper;

/**
 * Class View
 */
class View extends Generic
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        UrlHelper $urlHelper
    ) {
        $this->storeManager = $storeManager;
        $this->urlHelper    = $urlHelper;
        parent::__construct($context, $registry);
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'      => __('View'),
            'class'      => 'frontend-link',
            'class_name' => Container::SPLIT_BUTTON,
            'options'    => $this->getOptions(),
            'sort_order' => 1
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    private function getOptions()
    {
        $options      = [];
        $product      = $this->getProduct();
        $storeIds     = $product->getStoreIds();
        $storeList    = $this->storeManager->getStores();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        foreach ($storeList as $store) {
            if (!in_array($store->getId(), $storeIds)) {
                continue;
            }
            $onclick = sprintf(
                "window.open('%s', 'store_view_%s');",
                $this->urlHelper->getProductUrl($product->getId(), $store->getId(), $store->getCode()),
                $store->getCode()
            );
            $options[] = [
                'label'   => $store->getName(),
                'onclick' => $onclick,
                'class'   => 'frontend-link',
                'default' => $defaultStore && $defaultStore->getCode() == $store->getCode()
            ];
        }
        return $options;
    }
}
