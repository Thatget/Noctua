<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [16-10-2019]
 */

/**
 * Return product name in grid
 */
namespace SIT\ProductFaqNew\Ui\Component\Listing\Column;

use SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory;

class GetParentCategoryName extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var CollectionFactory
     */
    protected $faqCategoryFactory;

    public function __construct(
        CollectionFactory $faqCategoryFactory,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->faqCategoryFactory = $faqCategoryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            //echo "<pre>";
            foreach ($dataSource['data']['items'] as $key => $value) {

                $sitCatData = $this->faqCategoryFactory->create()->addFieldToSelect('*')->addFieldToFilter('parent_cat_name', ['eq' => $value['id']]);
                //$country = $this->_countryFactory->create()->loadByCode($value['review_country']);
                foreach ($sitCatData as $keyCat => $catValue) {
                    # code...
                    $dataSource['data']['items'][$key]['parent_cat_name'] = $catValue->getCatName() . '--------------' . $catValue->getParentCatName();
                }

                //$dataSource['data']['items'][$key]['parent_cat_name'] = rtrim($name, ', ');
            }
            //exit();
        }
        return $dataSource;
    }
}
