<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Code standard by : MD [10-07-2019]
 */

namespace SIT\CustomVariables\Block\System\Variable;

class Edit extends \Magento\Variable\Block\System\Variable\Edit
{
    /**
     * Prepare layout.
     * Adding save_and_continue button
     *
     * @return $this
     */
    protected function _preparelayout()
    {
        $this->addButton(
            'duplicate',
            [
                'label' => __('Duplicate'),
                'class' => 'save',
                'onclick' => 'setLocation(\'' . $this->getCustomUrl($this->getRequest()->getParam("variable_id")) . '\')',
            ],
            90
        );
        $this->addButton(
            'save_and_edit',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ],
            ],
            100
        );
        if (!$this->getVariable()->getId()) {
            $this->removeButton('delete');
        }
        return parent::_prepareLayout();
    }

    /**
     * URL getter
     *
     * @return string
     */
    public function getCustomUrl($varId)
    {
        return $this->getUrl('sit_customvariables/customvariable/duplicate/', ['variable_id' => $varId, 'edit_duplicate' => 1]);
    }

}
