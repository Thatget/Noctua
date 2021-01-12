<?php

namespace SIT\ProductVideoNew\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Language extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Eav\Model\Config $eavConfig,
        array $components = [],
        array $data = []
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * get selected language to grid
     * @param  array  $dataSource [description]
     * @return [type]             [description]
     */
    public function prepareDataSource(array $dataSource)
    {
        $attribute = $this->eavConfig->getAttribute('sit_productvideonew_productvideo', 'video_language');
        $language = $attribute->getSource()->getAllOptions();

        $options = [];

        foreach ($language as $option) {
            if (!empty($option['label'])) {
                if ($option['label'] != ' ') {
                    $options[$option['value']] = [
                        'label' => $option['label'],
                    ];
                }
            }
        }
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if (array_key_exists($items['video_language'], $options)) {
                    $items['video_language'] = $options[$items['video_language']]['label'];
                }
            }
        }
        return $dataSource;
    }
}
