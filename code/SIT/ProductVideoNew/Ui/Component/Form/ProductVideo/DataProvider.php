<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Ui\Component\Form\ProductVideo;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SIT\ProductVideoNew\Model\ResourceModel\ProductVideo\Collection;

class DataProvider extends AbstractDataProvider {
	/**
	 * @var Collection
	 */
	protected $collection;

	/**
	 * @var FilterPool
	 */
	protected $filterPool;

	/**
	 * @var array
	 */
	protected $loadedData;

	/**
	 * @var RequestInterface
	 */
	protected $request;

	/**
	 * @param string           $name             [description]
	 * @param string           $primaryFieldName [description]
	 * @param string           $requestFieldName [description]
	 * @param Collection       $collection       [description]
	 * @param FilterPool       $filterPool       [description]
	 * @param RequestInterface $request          [description]
	 * @param array            $meta             [description]
	 * @param array            $data             [description]
	 */
	// @codingStandardsIgnoreStart
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		Collection $collection,
		FilterPool $filterPool,
		RequestInterface $request,
		array $meta = [],
		array $data = []
	) {
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
		$this->collection = $collection;
		$this->filterPool = $filterPool;
		$this->request = $request;
	}
	// @codingStandardsIgnoreEnd
	/**
	 * Get data
	 *
	 * @return array
	 */
	public function getData() {
		if (!$this->loadedData) {
			$storeId = (int) $this->request->getParam('store');
			$this->collection->setStoreId($storeId)->addAttributeToSelect('*');
			$items = $this->collection->getItems();
			foreach ($items as $item) {
				$item->setStoreId($storeId);
				$this->loadedData[$item->getEntityId()] = $item->getData();
				break;
			}
		}
		return $this->loadedData;
	}
}
