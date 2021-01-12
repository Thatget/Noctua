<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [31-05-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class UnusedDuplicateActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * [__construct description]
     * @param ContextInterface                                   $context            [description]
     * @param UiComponentFactory                                 $uiComponentFactory [description]
     * @param UrlInterface                                       $urlBuilder         [description]
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession        [description]
     * @param array                                              $components         [description]
     * @param array                                              $data               [description]
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->coreSession = $coreSession;
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
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName . '_html'] = "<a href='#'>Duplicate</a>";
                $item[$fieldName . '_submitlabel'] = __('Duplicate');
                $item[$fieldName . '_cancellabel'] = __('Reset');
                $item[$fieldName . '_entityid'] = $item['entity_id'];
                $item[$fieldName . '_formaction'] = $this->urlBuilder->getUrl('sit_productcompatibility/unused/duplicate');
            }
        }
        return $dataSource;
    }
}
