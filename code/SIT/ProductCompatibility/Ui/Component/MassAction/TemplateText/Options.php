<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-05-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\MassAction\TemplateText;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;

class Options implements JsonSerializable
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * @var \SIT\TemplateText\Model\TemplateTextFactory
     */
    protected $templateCollectionFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * [__construct description]
     * @param UrlInterface                                                         $urlBuilder                [description]
     * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templateCollectionFactory [description]
     * @param \Magento\Framework\App\Request\Http                                  $request                   [description]
     * @param array                                                                $data                      [description]
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templateCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->request = $request;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $options = [];
            $storeId = $this->request->getParam('store', 0);
            $template = $this->templateCollectionFactory->create()->setStoreId($storeId)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1]);
            $options[0]['value'] = 0;
            $options[0]['label'] = '-- Please Select --';
            foreach ($template as $key => $item) {
                $options[$key]['value'] = $item->getId();
                $options[$key]['label'] = $item->getTemplateTextTitle();
            }
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'template_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }
            $this->options = array_values($this->options);
        }
        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
