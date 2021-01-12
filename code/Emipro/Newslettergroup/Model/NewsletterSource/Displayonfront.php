<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Model\NewsletterSource;

class Displayonfront extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => 1, 'label' => __('Enabled')],
                ['value' => 2, 'label' => __('Disabled')],
            ];
        }

        return $this->_options;
    }
}
