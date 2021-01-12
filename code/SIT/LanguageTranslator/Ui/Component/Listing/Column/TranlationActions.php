<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\LanguageTranslator\Ui\Component\Listing\Column;

class TranlationActions extends \Magento\Ui\Component\Listing\Columns\Column {
	protected $urlBuilder;

	const URL_EDIT_PATH = 'languagetranslator/index/edit';
	const URL_DELETE_PATH = 'languagetranslator/index/delete';

	public function __construct(
		\Magento\Framework\UrlInterface $urlBuilder,
		\Magento\Framework\View\Element\UiComponent\ContextInterface $context,
		\Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = []
	) {
		$this->urlBuilder = $urlBuilder;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as &$item) {
				if (isset($item['translation_id'])) {
					$item[$this->getData('name')] = [
						'edit' => [
							'href' => $this->urlBuilder->getUrl(
								static::URL_EDIT_PATH,
								[
									'translation_id' => $item['translation_id'],
								]
							),
							'label' => __('Edit'),
						],
						'delete' => [
							'href' => $this->urlBuilder->getUrl(
								static::URL_DELETE_PATH,
								[
									'translation_id' => $item['translation_id'],
								]
							),
							'label' => __('Delete'),
							'confirm' => [
								'title' => __('Delete'),
								'message' => __('Are you sure you wan\'t to delete a record?'),
							],
							'hidden' => false,
						],
					];
				}
			}
		}
		return $dataSource;
	}
}
