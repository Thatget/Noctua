<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Model;

class Language implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * get list of language for ui form
     * @return [type] [description]
     */
    public function toOptionArray()
    {
        $attribute = $this->eavConfig->getAttribute('sit_productvideonew_productvideo', 'video_language');
        $language = $attribute->getSource()->getAllOptions();

        $options = [];

        foreach ($language as $option) {
            if ($option['label'] != ' ') {
                $options[] = [
                    'label' => $option['label'],
                    'value' => $option['value'],
                ];
            }
        }
        return $options;
    }
}
