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
use Magento\Ui\Component\Listing\Columns\Column;

class SlideActions extends Column {
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
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = []
	) {
		$this->urlBuilder = $urlBuilder;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource) {

		if (isset($dataSource['data']['items'])) {
			$storeId = $this->context->getFilterParam('store_id');
			foreach ($dataSource['data']['items'] as &$item) {

				if (isset($item['entity_id'])) {
					$item[$this->getData('name')]['edit'] = [
						'href' => $this->urlBuilder->getUrl(
							self::URL_PATH_EDIT,
							['entity_id' => $item['entity_id'], 'store' => $storeId]
						),
						'label' => __('Edit'),
						'hidden' => false,
					];

					$item[$this->getData('name')]['delete'] = [
						'href' => $this->urlBuilder->getUrl(
							self::URL_PATH_DELETE,
							['entity_id' => $item['entity_id'], 'store' => $storeId]
						),
						'label' => __('Delete'),
						'hidden' => false,
					];
				}
			}
		}

		return $dataSource;
	}
}
