<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [10-06-2019]
 */
namespace SIT\ProductCompatibility\Model\Config\Source;

use Magento\Framework\App\RequestInterface;

class MassActionsList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * [__construct description]
     * @param RequestInterface                                   $request     [description]
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession [description]
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->request = $request;
        $this->coreSession = $coreSession;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $unused = $this->request->getParam('unused');
        if ($unused == 'unusedcomp') {
            $compType = $this->coreSession->getProCompType();
        } else {
            $compType = $this->request->getParam('comptype');
        }
        if ($compType == 'Mainboard' || $compType == 'RAM') {
            return [
                ['value' => 'change_socket', 'label' => __('Change Socket')],
                ['value' => 'change_manu', 'label' => __('Change Manufacturer')],
                ['value' => 'change_model', 'label' => __('Change Model')],
                ['value' => 'change_comp', 'label' => __('Change Compatibility')],
                ['value' => 'change_type', 'label' => __('Change Type')],
                ['value' => 'assign_products', 'label' => __('Assign Products')],
                ['value' => 'change_temp_text', 'label' => __('Assign Template Text')],
                ['value' => 'duplicate', 'label' => __('Duplicate')],
                ['value' => 'multi_action', 'label' => __('Multi Action')],
            ];
        }
        if ($compType == 'CPU') {
            return [
                ['value' => 'change_socket', 'label' => __('Change Socket')],
                ['value' => 'change_manu', 'label' => __('Change Manufacturer')],
                ['value' => 'change_model', 'label' => __('Change Model')],
                ['value' => 'change_series', 'label' => __('Change Series')],
                ['value' => 'change_comp', 'label' => __('Change Compatibility')],
                ['value' => 'change_type', 'label' => __('Change Type')],
                ['value' => 'assign_products', 'label' => __('Assign Products')],
                ['value' => 'change_temp_text', 'label' => __('Assign Template Text')],
                ['value' => 'duplicate', 'label' => __('Duplicate')],
                ['value' => 'multi_action', 'label' => __('Multi Action')],
            ];
        }
        if ($compType == 'Case') {
            return [
                ['value' => 'change_manu', 'label' => __('Change Manufacturer')],
                ['value' => 'change_model', 'label' => __('Change Model')],
                ['value' => 'change_comp', 'label' => __('Change Compatibility')],
                ['value' => 'change_type', 'label' => __('Change Type')],
                ['value' => 'assign_products', 'label' => __('Assign Products')],
                ['value' => 'change_temp_text', 'label' => __('Assign Template Text')],
                ['value' => 'duplicate', 'label' => __('Duplicate')],
                ['value' => 'multi_action', 'label' => __('Multi Action')],
            ];
        }
    }
}
