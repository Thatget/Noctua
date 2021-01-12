<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Onibi\StoreLocator\Model;

use Onibi\StoreLocator\Model\ResourceModel\Store\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {
	/**
	 * @var array
	 */
	protected $loadedData;

	/**
	 * @var Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @param string                                     $name                   [description]
	 * @param string                                     $primaryFieldName       [description]
	 * @param string                                     $requestFieldName       [description]
	 * @param CollectionFactory                          $storeCollectionFactory [description]
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager           [description]
	 * @param array                                      $meta                   [description]
	 * @param array                                      $data                   [description]
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $storeCollectionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		array $meta = [],
		array $data = []
	) {
		$this->collection = $storeCollectionFactory->create();
		$this->_storeManager = $storeManager;
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}

	public function getData() {
		if (isset($this->loadedData)) {
			return $this->loadedData;
		}
		$items = $this->collection->getItems();
		$mediaUrl = $this->_storeManager
			->getStore()
			->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		foreach ($items as $store) {
			$storeData = $store->getData();
			if (isset($storeData['image'])) {
				$imageName = explode('/', $storeData['image']);
				$storeData['image'] = array(
					[
						'name' => $imageName[2],
						'url' => $mediaUrl . $storeData['image'],
					],
				);
			} else {
				$storeData['image'] = null;
			}
			if (isset($storeData['marker'])) {
				$markerName = explode('/', $storeData['marker']);
				$storeData['marker'] = array(
					[
						'name' => $markerName[2],
						'url' => $mediaUrl . $storeData['marker'],
					],
				);
			} else {
				$storeData['marker'] = null;
			}
			$this->loadedData[$store->getId()] = $storeData;
		}
		return $this->loadedData;
	}
}
