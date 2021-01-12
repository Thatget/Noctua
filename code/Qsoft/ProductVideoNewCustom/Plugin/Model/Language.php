<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProductVideoNewCustom\Plugin\Model;

/**
 * Class Language
 *
 * @package Qsoft\ProductVideoNewCustom\Plugin\Model
 */
class Language
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * Language constructor.
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * Sort language alphabet
     *
     * @param \SIT\ProductVideoNew\Model\Language $subject
     * @param callable $proceed
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundToOptionArray(\SIT\ProductVideoNew\Model\Language $subject, callable $proceed) {
        $attribute = $this->eavConfig->getAttribute('sit_productvideonew_productvideo', 'video_language');
        $language = $attribute->getSource()->getAllOptions();

        $options = [];
        foreach ($language as $option) {
            if ($option['label'] != ' ') {
                $options[$option['label']] = [
                    'label' => $option['label'],
                    'value' => $option['value'],
                ];
            }
        }
        ksort($options);
        return $options;
    }
}
