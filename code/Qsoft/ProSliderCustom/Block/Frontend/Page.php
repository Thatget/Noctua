<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProSliderCustom\Block\Frontend;

use Mediarocks\ProSlider\Block\Frontend\Slide;
use Magento\Framework\Json\DecoderInterface;

/**
 * Class Page
 *
 * @package Qsoft\ProSliderCustom\Block\Frontend
 */
class Page
{
    /**
     * @var Slide
     */
    protected $slideCollection;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * Page constructor.
     *
     * @param Slide $slideCollection
     */
    public function __construct(
        Slide $slideCollection,
        DecoderInterface $jsonDecoder
    ) {
        $this->slideCollection = $slideCollection;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * Get slide data
     *
     * @param \Mediarocks\ProSlider\Block\Frontend\Page $subject
     * @param callable $proceed
     * @param $sliderCollection
     * @param $pageType
     * @param $dataValue
     * @param $store
     * @param $url_key
     * @return mixed
     */
    public function aroundGetSlideData(
        \Mediarocks\ProSlider\Block\Frontend\Page $subject,
        callable $proceed,
        $sliderCollection,
        $pageType,
        $dataValue,
        $store,
        $url_key
    ) {
        $img_url = [];
        if (isset($sliderCollection)) {
            $record_counter = 0;
            $i = 0;
            if ($url_key === $subject::PAGE_ACTION_NO_ROUTE) {
                $url_key = 'no-route';
            }
            foreach ($sliderCollection as $key => $value) {
                $data = $value->getData();
                if ($data['page_type'] === $pageType && $data['is_active'] == 1 && in_array($store, json_decode($data['store_id'])) && $i == 0) {
                    $sliders = [];
                    if (isset($data['slides']) && $data['slides']) {
                        $sliders = json_decode($data['slides']);
                        if (!$sliders) {
                            $data['slides'] = explode(",", $data['slides']);
                        } else {
                            $sliders = $this->jsonDecoder->decode($data['slides']);
                            if (is_array($sliders)) {
                                asort($sliders);
                                $data['slides'] = array_keys($sliders);
                            } else {
                                $data['slides'] = [$sliders];
                            }
                        }
                    }
                    $data[$dataValue] = json_decode(unserialize($data[$dataValue]));
                    if (in_array($url_key, $data[$dataValue])) {
                        $record_counter++;
                        if ($record_counter === 1) {
                            foreach ($data['slides'] as $slide_id) {
                                $data['slides'][$i] = $this->slideCollection->getSlideCollectionById($slide_id);
                                $i++;
                            }
                            return $data;
                        }
                    }
                }
            }
        }
    }
}
