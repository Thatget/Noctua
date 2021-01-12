<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\LanguageTranslator\Model;

use SIT\LanguageTranslator\Model\ResourceModel\Tranlation\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {
	/**
	 * @var array
	 */
	protected $loadedData;

	// @codingStandardsIgnoreStart
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $TranlationCollectionFactory,
		array $meta = [],
		array $data = []
	) {
		$this->collection = $TranlationCollectionFactory->create();
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}
	// @codingStandardsIgnoreEnd

	public function getData() {
		if (isset($this->loadedData)) {
			return $this->loadedData;
		}
		$items = $this->collection->getItems();
		foreach ($items as $Tranlation) {
			$this->loadedData[$Tranlation->getId()] = $Tranlation->getData();
		}
		return $this->loadedData;
	}
}
