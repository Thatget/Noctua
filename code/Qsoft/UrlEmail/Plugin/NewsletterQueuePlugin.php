<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\UrlEmail\Plugin;

/**
 * Plugin for \Magento\Newsletter\Model\Template
 */
class NewsletterQueuePlugin
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
    public function afterGetTemplateText(
        \Magento\Newsletter\Model\Template $subject,
        $result
    )
    {
        $result = $this->mailText->linkAllEmail($result);
        return $result;
    }
}