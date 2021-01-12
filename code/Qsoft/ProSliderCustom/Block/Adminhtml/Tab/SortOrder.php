<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProSliderCustom\Block\Adminhtml\Tab;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Mediarocks\ProSlider\Model\SliderFactory;
use Magento\Framework\Json\DecoderInterface;

class SortOrder extends AbstractRenderer
{
    /**
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecode;

    /**
     * SortOrder constructor.
     *
     * @param Context $context
     * @param SliderFactory $sliderFactory
     * @param DecoderInterface $jsonDecode
     * @param array $data
     */
    public function __construct(
        Context $context,
        SliderFactory $sliderFactory,
        DecoderInterface $jsonDecode,
        $data = []
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->jsonDecode = $jsonDecode;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param Object $row
     * @return  image with src
     */
    public function render(DataObject $row) {
        $sliderId = $this->getRequest()->getParam('entity_id');
        $slider = $this->sliderFactory->create()->load($sliderId);
        $slides = json_decode($slider['slides']);
        if (!$slides) {
            $slides = explode(',', $slider['slides']);
        } else {
            $slides = $this->jsonDecode->decode($slider['slides']);
        }
        if (isset($slides[$row->getId()])) {
            return '<div class="admin__grid-control"><input type="text" class="input-text validate-number" name="sort_order" value="'.$slides[$row->getId()].'" tabindex="1000"></div>';
        } else {
            return '<div class="admin__grid-control"><input type="text" class="input-text validate-number" name="sort_order" value="" tabindex="1000"></div>';
        }
    }
}
