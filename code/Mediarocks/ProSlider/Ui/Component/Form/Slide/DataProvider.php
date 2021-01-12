<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [20-03-2019]
 */
namespace Mediarocks\ProSlider\Ui\Component\Form\Slide;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mediarocks\ProSlider\Model\ResourceModel\Slide\Collection;

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
	 * @var UrlInterface
	 */
	protected $currentUrl;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

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
		StoreManagerInterface $storeManager,
		UrlInterface $currentUrl,
		array $meta = [],
		array $data = []
	) {
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
		$this->storeManager = $storeManager;
		$this->currentUrl = $currentUrl;
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
		if (!$this->loadedData) {
			$entity_id = $this->request->getParam('entity_id');
			$storeId = (int) $this->request->getParam('store');
			$this->collection
				->setStoreId($storeId)
				->addAttributeToSelect('*');
			$items = $this->collection->getItems();
			$media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			/* Added By MJ [For edit form data population] [01-04-2019] */
			foreach ($items as $item) {
				if ($item['entity_id'] == $entity_id) {
					$item->setStoreId($storeId);
					$url[0]['url'] = $media_path . '/' . $item['image'];
					$item['image'] = $url;
					if (isset($item['image_link'])) {
						$item['image_link'] = unserialize($item['image_link']);
					}
					$this->loadedData[$item->getId()] = $item->getData();
				}
			}
			/* End By MJ [For edit form data population] [01-04-2019] */
		}
		return $this->loadedData;
	}

	/**
	 * @param \Magento\Framework\Api\Filter $filter [description]
	 */
	public function addFilter(\Magento\Framework\Api\Filter $filter) {
		return;
	}
}
?>