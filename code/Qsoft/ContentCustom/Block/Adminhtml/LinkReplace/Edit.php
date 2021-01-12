<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qsoft\ContentCustom\Block\Adminhtml\LinkReplace;

use Magento\Framework\View\Element\AbstractBlock;
use Qsoft\ContentCustom\Model\LinkReplace as ModelLinkReplace;

/**
 * Newsletter queue edit block
 *
 * @api
 * @since 100.0.2
 */
class Edit extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Newsletter::queue/edit.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Retrieve current Newsletter Queue Object
     *
     * @return ModelQueue
     */
    public function getQueue()
    {
        return $this->_coreRegistry->registry('current_queue');
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->setChild(
            'form',
            $this->getLayout()->createBlock(\Qsoft\ContentCustom\Block\Adminhtml\LinkReplace\Form::class, 'form')
        );
        return parent::_beforeToHtml();
    }

    /**
     * Get the url for save
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Prepare for the layout
     *
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'back_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Back'),
                'onclick' => "window.location.href = '" . $this->getUrl( '*/*') . "'",
                'class' => 'action-back'
            ]
        );

        $this->getToolbar()->addChild(
            'reset_button',
            \Magento\Backend\Block\Widget\Button::class,
            ['label' => __('Reset'), 'class' => 'reset', 'onclick' => 'window.location = window.location']
        );

        $this->getToolbar()->addChild(
            'save_button',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Save Newsletter'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#queue_edit_form']],
                ]
            ]
        );

        $this->getToolbar()->addChild(
            'save_and_resume',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Save and Resume'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'save',
                            'target' => '#queue_edit_form',
                            'eventData' => ['action' => ['args' => ['_resume' => 1]]],
                        ],
                    ],
                ]
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve Back Button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Resume Button HTML
     *
     * @return string
     */
    public function getResumeButtonHtml()
    {
        return $this->getChildHtml('save_and_resume');
    }

    /**
     * Getter for single store mode check
     *
     * @return bool
     */
    protected function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Getter for id of current store (the only one in single-store mode and current in multi-stores mode)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function getStoreId()
    {
        return $this->_storeManager->getStore(true)->getId();
    }

    /**
     * Getter for header text
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHeaderText()
    {
        return __('Link Replace Add Or Edit');
    }
}
