<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-06-2019]
 */

namespace SIT\ProductReviewNew\Block\Adminhtml;

use Magento\Backend\Block\Template;
use SIT\ProductReviewNew\Model\Product as SitReviewProduct;

class AssignReview extends Template
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'products/assign_review.phtml';

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
     * @var SitReviewProduct
     */
    protected $sitReviewProduct;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context  $context          [description]
     * @param \Magento\Framework\Registry              $registry         [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder      [description]
     * @param SitReviewProduct                         $sitReviewProduct [description]
     * @param array                                    $data             [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        SitReviewProduct $sitReviewProduct,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->sitReviewProduct = $sitReviewProduct;
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
                'SIT\ProductReviewNew\Block\Adminhtml\Tab\Reviewgrid',
                'sit.productreviewnew.tab.reviewgrid'
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
        $tempSitReviewProduct = $this->sitReviewProduct;
        $collection = $tempSitReviewProduct->getCollection()->addFieldToSelect(['productreview_id', 'position'])->addFieldToFilter("product_id", ["eq" => $entity_id]);
        if (!empty($collection->getData())) {
            foreach ($collection as $reviewProducts) {
                $result[$reviewProducts['productreview_id']] = $reviewProducts['position'];
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
