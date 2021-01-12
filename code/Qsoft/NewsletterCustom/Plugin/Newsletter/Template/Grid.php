<?php

namespace Qsoft\NewsletterCustom\Plugin\Newsletter\Template;

class Grid extends \Magento\Newsletter\Block\Adminhtml\Template\Grid{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Newsletter\Model\ResourceModel\Template\Collection $templateCollection,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $templateCollection, $data);
        $this->setDefaultSort('template_code');
        $this->setDefaultDir('DESC');
    }
}