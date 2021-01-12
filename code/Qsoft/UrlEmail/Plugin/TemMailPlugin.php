<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\UrlEmail\Plugin;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Plugin for \Magento\Email\Model\Template
 */
class TemMailPlugin
{
    protected $mailText;

    public function __construct(
        \Qsoft\UrlEmail\Helper\DataMailtext $mailText
    )
    {
        $this->mailText = $mailText;
    }

    /**
     * @param \Magento\Newsletter\Model\Template $subject
     * @param $result
     * @return string|string[]|null
     */
    public function afterProcessTemplate(
        \Magento\Email\Model\Template $subject,
        $result
    )
    {
        $result = $this->mailText->linkAllEmail($result);
        return $result;
    }
}