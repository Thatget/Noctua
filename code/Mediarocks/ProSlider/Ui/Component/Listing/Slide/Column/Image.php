<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [25-3-2019]
 */

namespace Mediarocks\ProSlider\Ui\Component\Listing\Slide\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Image extends Column
{
	/**
	 * Url path
	 */
	const URL_PATH_EDIT = 'proslider/slide/edit';
	const URL_PATH_DELETE = 'proslider/slide/delete';

	/**
	 * @var UrlInterface
	 */
	protected $urlBuilder;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * [__construct description]
	 * @param ContextInterface   $context            [description]
	 * @param UiComponentFactory $uiComponentFactory [description]
	 * @param UrlInterface       $urlBuilder         [description]
	 * @param array              $components         [description]
	 * @param array              $data               [description]
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		StoreManagerInterface $storeManager,
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = []
	) {
		$this->urlBuilder = $urlBuilder;
		$this->storeManager = $storeManager;
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
		$media_path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		if (isset($dataSource['data']['items'])) {
			$storeId = $this->context->getFilterParam('store_id');
			foreach ($dataSource['data']['items'] as &$item) {

				if (isset($item['entity_id'])) {
					$src = $media_path . '';
					if (array_key_exists('image', $item)) {
						$src = $media_path . '' . $item['image'];
						$item['image'] = '<img src="' . $src . '" />';
					}

					// $item['image'] = '<img src="' . $src . '" />';
				}
			}
		}
		return $dataSource;
	}
}
