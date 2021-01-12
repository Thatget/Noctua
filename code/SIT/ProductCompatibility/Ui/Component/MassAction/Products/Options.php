<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-05-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\MassAction\Products;

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
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * [__construct description]
     * @param UrlInterface                                                   $urlBuilder        [description]
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory [description]
     * @param array                                                          $data              [description]
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->collectionFactory = $collectionFactory;
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
            $productCollection = $this->collectionFactory->create()->addAttributeToSelect('*');
            foreach ($productCollection as $product) {
                $options[] = ['name' => $product->getName(), 'id' => $product->getId()];
            }
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['id']] = [
                    'type' => 'products_' . $optionCode['id'],
                    'label' => $optionCode['name'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['id']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['id']]
                    );
                }

                $this->options[$optionCode['id']] = array_merge_recursive(
                    $this->options[$optionCode['id']],
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
