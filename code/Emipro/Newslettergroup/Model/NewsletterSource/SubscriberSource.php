<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Emipro\Newslettergroup\Model\NewsletterSource;

class SubscriberSource implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * [__construct description]
     * @param \Magento\Eav\Model\Config $eavConfig [description]
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    public function toOptionArray()
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'newsletter_website');
        return $attribute->getSource()->getAllOptions();
    }
}
