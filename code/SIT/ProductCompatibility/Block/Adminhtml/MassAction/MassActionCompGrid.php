<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\MassAction;

use Magento\Backend\Block\Template;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;

class MassActionCompGrid extends Template
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'compatibility/massaction/assign_comps.phtml';

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
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context  $context            [description]
     * @param \Magento\Framework\Registry              $registry           [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder        [description]
     * @param ProductCompatibilityFactory              $productcompFactory [description]
     * @param array                                    $data               [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        ProductCompatibilityFactory $productcompFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->productcompFactory = $productcompFactory;
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
                'SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Compatibilitygrid',
                'sit.productcompatibility.massaction.tab.compatibilitygrid'
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

    public function getItem()
    {
        return $this->registry->registry('entity_id');
    }
}
