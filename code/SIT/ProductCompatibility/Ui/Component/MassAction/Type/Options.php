<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-05-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\MassAction\Type;

use Magento\Framework\UrlInterface;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
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
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * [__construct description]
     * @param UrlInterface      $urlBuilder     [description]
     * @param ProductCompHelper $prodCompHelper [description]
     * @param array             $data           [description]
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ProductCompHelper $prodCompHelper,
        array $data = []
    ) {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->prodCompHelper = $prodCompHelper;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $compTypeInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
            $compTypeId = $compTypeInfo->getAttributeId();
            $compTypeAllOption = $this->prodCompHelper->getAttributeOptionAll($compTypeId);
            $this->prepareData();
            foreach ($compTypeAllOption as $optionCode) {
                $this->options[$optionCode['option_id']] = [
                    'type' => 'compatibility_' . $optionCode['option_id'],
                    'label' => $optionCode['value'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['option_id']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['option_id']]
                    );
                }

                $this->options[$optionCode['option_id']] = array_merge_recursive(
                    $this->options[$optionCode['option_id']],
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
