<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Model;

use Emipro\Newslettergroup\Model\ResourceModel\Newsletter\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {
	/**
	 * @var array
	 */
	protected $_loadedData;

	/**
	 * @var CollectionFactory
	 */
	protected $newsLetterCollectionFactory;

	/**
	 * [__construct description]
	 * @param string                                     $name                   [description]
	 * @param string                                     $primaryFieldName       [description]
	 * @param string                                     $requestFieldName       [description]
	 * @param CollectionFactory                          $storeCollectionFactory [description]
	 * @param array                                      $meta                   [description]
	 * @param array                                      $data                   [description]
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $newsLetterCollectionFactory,
		array $meta = [],
		array $data = []
	) {
		$this->collection = $newsLetterCollectionFactory->create();
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}

	public function getData() {
		if (isset($this->_loadedData)) {
			return $this->_loadedData;
		}
		$items = $this->collection->getItems();
		foreach ($items as $Newsletter) {
			$this->_loadedData[$Newsletter->getId()] = $Newsletter->getData();
		}
		return $this->_loadedData;
	}
}
