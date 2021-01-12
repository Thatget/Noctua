<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

/**
 * Return product name in grid
 */
namespace SIT\ProductCompatibility\Ui\Component\Listing\Column;

class GetTemplateText extends \Magento\Ui\Component\Listing\Columns\Column {

	/**
	 * @var \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory
	 */
	protected $templatetextFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\UiComponent\ContextInterface         $context             [description]
	 * @param \Magento\Framework\View\Element\UiComponentFactory                   $uiComponentFactory  [description]
	 * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory [description]
	 * @param array                                                                $components          [description]
	 * @param array                                                                $data                [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\UiComponent\ContextInterface $context,
		\Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
		\SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory,
		array $components = [],
		array $data = []
	) {
		$this->templatetextFactory = $templatetextFactory;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	public function prepareDataSource(array $dataSource) {
		if (isset($dataSource['data']['items'])) {
			$storeId = $this->getContext()->getRequestParam('store', 0);
			foreach ($dataSource['data']['items'] as $key => $value) {
				$name = '';
				if (isset($value['template_text_1']) && $value['template_text_1'] != '-1') {
					$templateText1 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_1']])->getFirstItem();
					$name .= $templateText1->getTemplateTextTitle() . " (T1),";
				}
				if (isset($value['template_text_2']) && $value['template_text_2'] != '-1') {
					$templateText2 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_2']])->getFirstItem();
					$name .= $templateText2->getTemplateTextTitle() . " (T2),";
				}
				if (isset($value['template_text_3']) && $value['template_text_3'] != '-1') {
					$templateText3 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_3']])->getFirstItem();
					$name .= $templateText3->getTemplateTextTitle() . " (T3),";
				}
				if (isset($value['template_text_4']) && $value['template_text_4'] != '-1') {
					$templateText4 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $value['template_text_4']])->getFirstItem();
					$name .= $templateText4->getTemplateTextTitle() . " (T4),";
				}
				$dataSource['data']['items'][$key]['template_text'] = rtrim($name, ', ');
			}
		}
		return $dataSource;
	}
}
