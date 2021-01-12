<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [21-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\Catalog;

use Magento\Backend\Block\Template;
use SIT\ProductCompatibility\Model\Product as SitCompProduct;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;

class AssignComp extends Template
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'compatibility/catalog/assign_procomps.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var ProductCompatibilityFactory
     */
    protected $productcompFactory;

    /**
     * @var SitCompProduct
     */
    protected $sitCompProduct;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context  $context            [description]
     * @param \Magento\Framework\Registry              $registry           [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder        [description]
     * @param ProductCompatibilityFactory              $productcompFactory [description]
     * @param SitCompProduct                           $sitCompProduct     [description]
     * @param array                                    $data               [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        ProductCompatibilityFactory $productcompFactory,
        SitCompProduct $sitCompProduct,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->productcompFactory = $productcompFactory;
        $this->sitCompProduct = $sitCompProduct;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Compatibilitygrid',
                'sit.productcompatibility.catalog.tab.compatibilitygrid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $entity_id = $this->getRequest()->getParam('id');
        $tempSitCompProduct = $this->sitCompProduct;
        $collection = $tempSitCompProduct->getCollection()->addFieldToSelect(['productcompatibility_id', 'position'])->addFieldToFilter("product_id", ["eq" => $entity_id]);
        if (!empty($collection->getData())) {
            foreach ($collection as $compProducts) {
                $result[$compProducts['productcompatibility_id']] = $compProducts['position'];
            }
            return $this->jsonEncoder->encode($result);
        }
        return '{}';
    }

    public function getItem()
    {
        return $this->registry->registry('my_item');
    }
}
