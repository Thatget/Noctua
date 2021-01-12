<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [13-06-2019]
 */
namespace SIT\Customfield\Plugin\Block\Adminhtml\Product\Attribute\Edit\Tab;

class Front
{

    /**
     * \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * [__construct description]
     * @param \Magento\Framework\Registry $registry [description]
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->coreRegistry = $registry;
    }
    /**
     * [aroundGetFormHtml description]
     * @param  \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front $subject [description]
     * @param  \Closure                                                          $proceed [description]
     * @return [type]                                                                     [description]
     */
    public function aroundGetFormHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front $subject,
        \Closure $proceed
    ) {
        $attributeObject = $this->coreRegistry->registry('entity_attribute');
        $form = $subject->getForm();
        $fieldset = $form->getElement('front_fieldset');
        $fieldset->addField(
            'label_link',
            'text',
            [
                'name' => 'label_link',
                'label' => __('URL for Link'),
                'title' => __('URL for Link'),
            ]
        );
        $form->setValues($attributeObject->getData());
        return $proceed();
    }
}
