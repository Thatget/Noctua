<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [29-04-2019]
 */

namespace SIT\WebformApi\Model;

use SIT\WebformApi\Model\ResourceModel\Countryprice\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {
	/**
	 * @var array
	 */
	protected $loadedData;

	/**
	 * @param string            $name                          [description]
	 * @param string            $primaryFieldName              [description]
	 * @param string            $requestFieldName              [description]
	 * @param CollectionFactory $CountrypriceCollectionFactory [description]
	 * @param array             $meta                          [description]
	 * @param array             $data                          [description]
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $CountrypriceCollectionFactory,
		array $meta = [],
		array $data = []
	) {
		$this->collection = $CountrypriceCollectionFactory->create();
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}

	public function getData() {
		if (isset($this->loadedData)) {
			return $this->loadedData;
		}
		$items = $this->collection->getItems();
		foreach ($items as $Countryprice) {
			$this->loadedData[$Countryprice->getId()] = $Countryprice->getData();
		}
		return $this->loadedData;
	}
}
