<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Model\Config\Source;

class GetTemplate implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \SIT\TemplateText\Model\TemplateTextFactory
     */
    protected $templateCollectionFactory;

    protected $request;

    /**
     * [__construct description]
     * @param \SIT\TemplateText\Model\TemplateTextFactory $templateCollectionFactory [description]
     */
    public function __construct(
        \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templateCollectionFactory,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->request = $request;
    }

    /**
     * Return options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $storeId = $this->request->getParam('store', 0);
        $template = $this->templateCollectionFactory->create()->setStoreId($storeId)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1]);
        $options[0]['value'] = -1;
        $options[0]['label'] = '-- Please Select --';
        foreach ($template as $key => $item) {
            $options[$key]['value'] = $item->getId();
            $options[$key]['label'] = $item->getTemplateTextTitle();
        }
        return $options;
    }
}
