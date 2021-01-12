<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [11-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\Coolers\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DuplicateButton
 */
class DuplicateButton implements ButtonProviderInterface
{
    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * RequestInterface
     * @var $request
     */
    protected $request;

    /**
     * [__construct description]
     * @param Context                                 $context  [description]
     * @param Registry                                $registry [description]
     * @param \Magento\Framework\App\RequestInterface $request  [description]
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->registry = $registry;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->registry->registry('entity_id') > 0) {
            $entity_id = $this->request->getParam('entity_id');
            return [
                'label' => __('Duplicate'),
                'on_click' => '',
                'class' => 'btn-duplicate',
                'sort_order' => 10,
            ];
        }
    }
}
