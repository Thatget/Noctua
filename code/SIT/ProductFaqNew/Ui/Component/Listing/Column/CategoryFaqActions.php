<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\ProductFaqNew\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

error_reporting(error_reporting() & ~E_NOTICE);

class CategoryFaqActions extends Column
{
	/**
	 * Url path
	 */
	const URL_PATH_EDIT = 'sit_productfaqnew/category/edit';
	const URL_PATH_DELETE = 'sit_productfaqnew/category/delete';

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
	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) {
			$storeId = $this->getContext()->getRequestParam('store');
			foreach ($dataSource['data']['items'] as &$item) {
				if (isset($item['id'])) {
					$item[$this->getData('name')]['edit'] = [
						'href' => $this->urlBuilder->getUrl(
							self::URL_PATH_EDIT,
							['id' => $item['id'], 'store' => $storeId]
						),
						'label' => __('Edit'),
						'hidden' => false,
					];
					$item[$this->getData('name')]['delete'] = [
						'href' => $this->urlBuilder->getUrl(
							self::URL_PATH_DELETE,
							['id' => $item['id'], 'store' => $storeId]
						),
						'label' => __('Delete'),
						'confirm' => [
							'title' => __('Delete ' . $item['id']),
							'message' => __('Are you sure you wan\'t to delete a ' . $item['id'] . ' record?'),
						],
						'hidden' => false,
					];
				}
			}
		}

		return $dataSource;
	}
}
