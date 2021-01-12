<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Ui\Component\MassAction\Language;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;

class Language implements JsonSerializable
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
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->data = $data;
        $this->eavConfig = $eavConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $attribute = $this->eavConfig->getAttribute('sit_productvideonew_productvideo', 'video_language');
            $options = $attribute->getSource()->getAllOptions();

            $this->prepareData();
            foreach ($options as $optionCode) {
                if ($optionCode['label'] != ' ') {

                    $this->options[$optionCode['value']] = [
                        'type' => 'language_' . $optionCode['value'],
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
