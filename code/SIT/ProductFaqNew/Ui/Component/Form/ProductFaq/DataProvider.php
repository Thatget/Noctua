<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductFaqNew\Ui\Component\Form\ProductFaq;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\Collection;

class DataProvider extends AbstractDataProvider
{
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
	public function getData()
	{
		if (!$this->loadedData) {
			$storeId = (int) $this->request->getParam('store');
			$this->collection->setStoreId($storeId)->addAttributeToSelect('*');
			$items = $this->collection->getItems();
			foreach ($items as $item) {
				$item->setStoreId($storeId);
				/*Changed by MD for set existing category collection on edit time[START][19-09-2019]*/
				$catIds = explode(',', $item->getCategoryId());
				$item->setCategoryId($catIds);
				/*Changed by MD for set existing category collection on edit time[END][19-09-2019]*/
				$this->loadedData[$item->getEntityId()] = $item->getData();
				break;
			}
		}
		return $this->loadedData;
	}

	/**
	 * Code for Add "Use Default Value" Checkbox in UI Form
	 */
	// public function getMeta()
	// {
	// 	$meta = parent::getMeta();
	// 	$meta['main_fieldset']['children']['faq_que']['arguments']['data']['config']['service']['template'] = 'ui/form/element/helper/service';
	// 	$meta['main_fieldset']['children']['faq_que']['arguments']['data']['config']['disabled'] = 1;
	// 	return $meta;
	// }
}
