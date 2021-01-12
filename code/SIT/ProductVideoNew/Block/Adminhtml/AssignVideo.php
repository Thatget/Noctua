<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-06-2019]
 */

namespace SIT\ProductVideoNew\Block\Adminhtml;

use Magento\Backend\Block\Template;
use SIT\ProductVideoNew\Model\Product as SitVideoProduct;

class AssignVideo extends Template
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'products/assign_video.phtml';

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
     * @var SitVideoProduct
     */
    protected $sitVideoProduct;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context  $context       [description]
     * @param \Magento\Framework\Registry              $registry      [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder   [description]
     * @param SitVideoProduct                            $sitVideoProduct [description]
     * @param array                                    $data          [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        SitVideoProduct $sitVideoProduct,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->sitVideoProduct = $sitVideoProduct;
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
                'SIT\ProductVideoNew\Block\Adminhtml\Tab\Videogrid',
                'sit.productvideonew.tab.videogrid'
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
        $tempSitVideoProduct = $this->sitVideoProduct;
        $collection = $tempSitVideoProduct->getCollection()->addFieldToSelect(['productvideo_id', 'position'])->addFieldToFilter("product_id", ["eq" => $entity_id]);
        if (!empty($collection->getData())) {
            foreach ($collection as $videoProducts) {
                $result[$videoProducts['productvideo_id']] = $videoProducts['position'];
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
