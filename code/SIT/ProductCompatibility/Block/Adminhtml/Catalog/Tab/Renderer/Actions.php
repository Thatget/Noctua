<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [06-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer;

use Magento\Framework\DataObject;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

class Actions extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;

    /**
     * \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * [__construct description]
     * @param \Magento\Backend\Model\UrlInterface     $urlBuilder     [description]
     * @param ProductCompHelper                       $prodCompHelper [description]
     * @param \Magento\Catalog\Model\ProductFactory   $_productloader [description]
     * @param \Magento\Framework\App\RequestInterface $request        [description]
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        ProductCompHelper $prodCompHelper,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->prodCompHelper = $prodCompHelper;
        $this->_productloader = $_productloader;
        $this->request = $request;
    }

    /**
     * [render description]
     * @param  DataObject $row [description]
     * @return [type]          [description]
     */
    public function render(DataObject $row)
    {
        $proId = $this->request->getParam('id');
        $product = $this->_productloader->create()->load($proId);
        $attrSetId = $product->getAttributeSetId();

        $typeInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $typeId = $typeInfo->getAttributeId();
        $typeAllOption = $this->prodCompHelper->getAttributeOptionAll($typeId);

        foreach ($typeAllOption as $key => $item) {
            $typeArr[$item->getOptionId()] = $item->getValue();
        }

        if (array_key_exists($row->getCompType(), $typeArr)) {
            $typeArr[$row->getCompType()];
        }

        if ($typeArr[$row->getCompType()] == 'Case') {
            if ($attrSetId == 34) {
                $href = 'sit_productcompatibility/coolers/edit/entity_id/' . $row->getEntityId() . '/';
            }
            if ($attrSetId == 36) {
                $href = 'sit_productcompatibility/fans/edit/entity_id/' . $row->getEntityId() . '/';
            }
        } else {
            $href = 'sit_productcompatibility/' . $typeArr[$row->getCompType()] . '/edit/entity_id/' . $row->getEntityId() . '/';
        }
        $url = $this->urlBuilder->getUrl($href);
        return '<a class="sit-admin-anchor-button" href="' . $url . '" target="_blank">' . __('Edit') . '</a>';
    }
}
