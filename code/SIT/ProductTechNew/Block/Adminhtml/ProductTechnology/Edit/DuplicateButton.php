<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [27-03-2019]
 */
namespace SIT\ProductTechNew\Block\Adminhtml\ProductTechnology\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DuplicateButton
 */
class DuplicateButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

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
        $this->urlBuilder = $context->getUrlBuilder();
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
                'on_click' => sprintf("location.href = '%s';", $this->getDuplicateUrl('*/*/duplicate', ['entity_id' => $entity_id, 'edit_duplicate' => 1])),
                'sort_order' => 10,
            ];
        }
    }

    /**
     * Get URL for duplicate (reset) button
     *
     * @return string
     */
    public function getDuplicateUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
