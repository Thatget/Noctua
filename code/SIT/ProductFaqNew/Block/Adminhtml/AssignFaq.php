<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-06-2019]
 */

namespace SIT\ProductFaqNew\Block\Adminhtml;

use Magento\Backend\Block\Template;
use SIT\ProductFaqNew\Model\Product as SitFaqProduct;

class AssignFaq extends Template
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'products/assign_faq.phtml';

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
     * @var SitFaqProduct
     */
    protected $sitFaqProduct;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context  $context       [description]
     * @param \Magento\Framework\Registry              $registry      [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder   [description]
     * @param SitFaqProduct                            $sitFaqProduct [description]
     * @param array                                    $data          [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        SitFaqProduct $sitFaqProduct,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->sitFaqProduct = $sitFaqProduct;
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
                'SIT\ProductFaqNew\Block\Adminhtml\Tab\Faqgrid',
                'sit.productfaqnew.tab.faqgrid'
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
        $tempSitFaqProduct = $this->sitFaqProduct;
        $collection = $tempSitFaqProduct->getCollection()->addFieldToSelect(['productfaq_id', 'position'])->addFieldToFilter("product_id", ["eq" => $entity_id]);
        if (!empty($collection->getData())) {
            foreach ($collection as $faqProducts) {
                $result[$faqProducts['productfaq_id']] = $faqProducts['position'];
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
