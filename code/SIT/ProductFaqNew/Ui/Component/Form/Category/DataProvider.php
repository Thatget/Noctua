<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Ui\Component\Form\Category;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SIT\ProductFaqNew\Model\ResourceModel\Category\Collection;

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

	/**
	 * Get data
	 *
	 * @return array
	 */
	public function getData() {
		if (isset($this->loadedData)) {
			return $this->loadedData;
		}
		$items = $this->collection->getItems();
		foreach ($items as $Category) {
			$this->loadedData[$Category->getId()] = $Category->getData();
		}
		return $this->loadedData;
	}
}
