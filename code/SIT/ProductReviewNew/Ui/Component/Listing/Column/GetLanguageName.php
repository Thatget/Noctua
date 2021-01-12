<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

/**
 * Return language name in grid
 */
namespace SIT\ProductReviewNew\Ui\Component\Listing\Column;

class GetLanguageName extends \Magento\Ui\Component\Listing\Columns\Column {
	/**
	 * @var \Magento\Eav\Model\Config
	 */
	protected $eavConfig;

	/**
	 * [__construct description]
	 * @param \Magento\Eav\Model\Config                                    $eavConfig          [description]
	 * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            [description]
	 * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory [description]
	 * @param array                                                        $components         [description]
	 * @param array                                                        $data               [description]
	 */
	public function __construct(
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Framework\View\Element\UiComponent\ContextInterface $context,
		\Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
		array $components = [],
		array $data = []
	) {
		$this->eavConfig = $eavConfig;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			foreach ($dataSource['data']['items'] as $key => $value) {
				$attribute = $this->eavConfig->getAttribute(\SIT\ProductReviewNew\Setup\ProductReviewSetup::ENTITY_TYPE_CODE, 'r_lng');
				if ($attribute->usesSource()) {
					$dataSource['data']['items'][$key]['r_lng'] = $attribute->getSource()->getOptionText($value['r_lng']);
				}
			}
		}
		return $dataSource;
	}
}
