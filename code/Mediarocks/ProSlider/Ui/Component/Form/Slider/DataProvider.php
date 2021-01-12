<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [20-03-2019]
 */
namespace Mediarocks\ProSlider\Ui\Component\Form\Slider;

use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use \Magento\Ui\DataProvider\AbstractDataProvider;
use \Mediarocks\ProSlider\Model\SliderFactory;

class DataProvider extends AbstractDataProvider {

	/**
	 * @var SliderFactory
	 */
	protected $sliderFactory;

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
	 * @param SliderFactory    $sliderFactory    [description]
	 * @param FilterPool       $filterPool       [description]
	 * @param RequestInterface $request          [description]
	 * @param array            $meta             [description]
	 * @param array            $data             [description]
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		SliderFactory $sliderFactory,
		FilterPool $filterPool,
		RequestInterface $request,
		array $meta = [],
		array $data = []
	) {
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
		$this->sliderFactory = $sliderFactory;
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
			/* Added By MJ [For edit form data population] [01-04-2019] */
			if (isset($entity_id)) {
				$sliderCollection = $this->sliderFactory->create()->load($entity_id)->getData();
				$sliderCollection['store_id'] = json_decode($sliderCollection['store_id']);
				if (isset($sliderCollection['sku'])) {
					$sliderCollection['sku'] = json_decode(unserialize($sliderCollection['sku']));
					$this->loadedData[$entity_id] = $sliderCollection;
				}
				if (isset($sliderCollection['category'])) {
					$sliderCollection['category'] = json_decode(unserialize($sliderCollection['category']));
					$this->loadedData[$entity_id] = $sliderCollection;
				}
				if (isset($sliderCollection['cms_page'])) {
					$sliderCollection['cms_page'] = json_decode(unserialize($sliderCollection['cms_page']));
					$this->loadedData[$entity_id] = $sliderCollection;
				}
				if (isset($sliderCollection['custom_page'])) {
					$sliderCollection['custom_page'] = implode(',', json_decode(unserialize($sliderCollection['custom_page'])));
				}
				if (isset($sliderCollection['store_id'])) {
					if (in_array(0, $sliderCollection['store_id'])) {
						$sliderCollection['store_id'] = 0;
					}
				}
				$this->loadedData[$entity_id] = $sliderCollection;
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