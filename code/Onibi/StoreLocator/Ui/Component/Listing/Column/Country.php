<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Country extends Column
{

    /**
     * @var Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * [__construct description]
     * @param ContextInterface                        $context            [description]
     * @param UiComponentFactory                      $uiComponentFactory [description]
     * @param \Magento\Directory\Model\CountryFactory $countryFactory     [description]
     * @param array                                   $components         [description]
     * @param array                                   $data               [description]
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['country_id'])) {
                    $country = $this->_countryFactory->create()->loadByCode($item['country_id']);
                    $countryName = $country->getName();
                    $item['country_id'] = $countryName;
                }
            }
        }

        return $dataSource;
    }
}
