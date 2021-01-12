<?php
/**
 * Copyright © Qsoft. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\NewsletterCustom\Plugin\Newsletter\Model;

/**
 * Class Template
 *
 * @package Qsoft\NewsletterCustom\Plugin\Newsletter\Model
 */
class Template
{
    /**
     * @var \Qsoft\NewsletterCustom\Service\NewsletterService
     */
    protected $newsletterService;

    /**
     * Template constructor.
     *
     * @param \Qsoft\NewsletterCustom\Service\NewsletterService $newsletterService
     */
    public function __construct(
        \Qsoft\NewsletterCustom\Service\NewsletterService $newsletterService
    ) {
        $this->newsletterService = $newsletterService;
    }

    /**
     * Process template text
     *
     * @param \Magento\Newsletter\Model\Template $subject
     * @param string $result
     * @return string|string[]
     */
    public function afterGetTemplateText(
        \Magento\Newsletter\Model\Template $subject,
        $result
    ) {
        $result = $this->newsletterService->processNewsletterText($result);
        return $result;
    }
}
