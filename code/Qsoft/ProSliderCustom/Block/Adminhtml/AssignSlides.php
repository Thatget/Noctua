<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProSliderCustom\Block\Adminhtml;

use Magento\Backend\Block\Template;

class AssignSlides extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'slides/assign_slides.phtml';

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
     * @var \Mediarocks\ProSlider\Model\SliderFactory
     */
    protected $_sliderCollectionFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context   $context                 [description]
     * @param \Magento\Framework\Registry               $registry                [description]
     * @param \Magento\Framework\Json\EncoderInterface  $jsonEncoder             [description]
     * @param \Mediarocks\ProSlider\Model\SliderFactory $sliderCollectionFactory [description]
     * @param array                                     $data                    [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Mediarocks\ProSlider\Model\SliderFactory $sliderCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->_sliderCollectionFactory = $sliderCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid() {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Qsoft\ProSliderCustom\Block\Adminhtml\Tab\Slidegrid',
                'qsoft.proslidercustom.tab.productgrid'
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
    public function getProductsJson() {

        $entity_id = $this->getRequest()->getParam('entity_id');

        $sliderCollectionFactory = $this->_sliderCollectionFactory->create()->load($entity_id);
        if (isset($sliderCollectionFactory) && $sliderCollectionFactory['slides']) {
            $slides = $sliderCollectionFactory['slides'];
            if (strpos($slides, "}")) {
                $result = $slides;
            } else {
                $slidesArr = [];
                if (strpos($slides, ",") > 0) {
                    $slideArr = explode(",", $slides);
                    $slidesArr = [];
                    foreach ($slideArr as $slide) {
                        $slidesArr[$slide] = "";
                    }
                    $result = json_encode($slidesArr);
                } else {
                    $slidesArr[$slides] = "";
                    $result = json_encode($slidesArr);
                }
            }
            return $result;
        }
        return '{}';
    }

    public function getItem()
    {
        return $this->registry->registry('my_item');
    }
}