<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory as CollectionCompFactory;

class Compatibilitygrid extends Extended
{

    /**
     * sit_productcompatibility_productcompatibility_product
     */
    const SIT_PRODUCTCOMPATIBILITY_PRODUCTCOMPATIBILITY_PRODUCT = 'sit_productcompatibility_productcompatibility_product';

    /**
     * category product table name for join product name
     */
    const CATEGORY_PRODUCT_ENTITY_VARCHAR = 'catalog_product_entity_varchar';

    /**
     * product name attribute ID
     */
    const PRODUCT_NAME_ATTRIBUTE_ID = '71';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * @var CollectionFactory
     */
    protected $setsFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionCompFactory
     */
    protected $collectionCompFactory;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * [__construct description]
     * @param Context                                            $context               [description]
     * @param Data                                               $backendHelper         [description]
     * @param Registry                                           $registry              [description]
     * @param Manager                                            $moduleManager         [description]
     * @param CollectionFactory                                  $setsFactory           [description]
     * @param StoreManagerInterface                              $storeManager          [description]
     * @param CollectionCompFactory                              $collectionCompFactory [description]
     * @param ProductCompHelper                                  $prodCompHelper        [description]
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession           [description]
     * @param array                                              $data                  [description]
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $registry,
        Manager $moduleManager,
        CollectionFactory $setsFactory,
        StoreManagerInterface $storeManager,
        CollectionCompFactory $collectionCompFactory,
        ProductCompHelper $prodCompHelper,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->_coreRegistry = $registry;
        $this->moduleManager = $moduleManager;
        $this->_setsFactory = $setsFactory;
        $this->_storeManager = $storeManager;
        $this->collectionCompFactory = $collectionCompFactory;
        $this->prodCompHelper = $prodCompHelper;
        $this->coreSession = $coreSession;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * [_construct description]
     * @return [type] [description]
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productcomp_grid_products');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * [get store id]
     *
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $this->coreSession->setProCompStore($storeId);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * [prepareCollection for grid]
     * @return collection
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $ids = $this->registry->registry('entity_id');
        $unused = $this->getRequest()->getParam('unused');
        if ($unused == 'unusedcomp') {
            $compType = $this->coreSession->getProCompType();
        } else {
            $compType = $this->getRequest()->getParam('comptype');
        }
        if ($ids != '') {
            $this->coreSession->setProCompId($ids);
        }
        if ($ids == '') {
            $ids = $this->coreSession->getProCompId();
        }
        $collection = $this->collectionCompFactory->create()->setStoreId($store)->addFieldToSelect('*');
        $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        $optionId = $this->prodCompHelper->getAttrOptionId($modelAllOption, trim($compType));
        $collection->addAttributeToFilter('comp_type', $optionId);
        //Get Product Name[START]
        $collection->getSelect()->joinLeft(
            ['t2' => self::SIT_PRODUCTCOMPATIBILITY_PRODUCTCOMPATIBILITY_PRODUCT],
            'e.entity_id = t2.productcompatibility_id',
            't2.product_id'
        );
        $collection->getSelect()->joinLeft(
            ['pv' => self::CATEGORY_PRODUCT_ENTITY_VARCHAR],
            't2.product_id = pv.entity_id and pv.attribute_id = ' . self::PRODUCT_NAME_ATTRIBUTE_ID,
            'substring_index(GROUP_CONCAT(DISTINCT pv.value SEPARATOR \',\'),\',\',4) as pname'
        );
        $collection->getSelect()->group('e.entity_id');
        //Get Product Name[END]
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        /**
         * For Filter Options[START]
         */
        $status = [1 => 'Enabled', 0 => 'Disabled'];
        $unused = $this->getRequest()->getParam('unused');
        if ($unused == 'unusedcomp') {
            $compType = $this->coreSession->getProCompType();
        } else {
            $compType = $this->getRequest()->getParam('comptype');
        }
        //Comp Socket
        $socketInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
        $socketId = $socketInfo->getAttributeId();
        $socketAllOption = $this->prodCompHelper->getAttributeOptionAll($socketId);
        foreach ($socketAllOption as $key => $item) {
            $socketArr[$item->getOptionId()] = $item->getValue();
        }
        //Comp Manufacturer
        $manuInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
        $manuId = $manuInfo->getAttributeId();
        $manuAllOption = $this->prodCompHelper->getAttributeOptionAll($manuId);
        foreach ($manuAllOption as $key => $item) {
            $manuArr[$item->getOptionId()] = $item->getValue();
        }
        //Comp Model
        $modelInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
        $modelId = $modelInfo->getAttributeId();
        $modelAllOption = $this->prodCompHelper->getAttributeOptionAll($modelId);
        foreach ($modelAllOption as $key => $item) {
            $modelArr[$item->getOptionId()] = $item->getValue();
        }
        //Comp Series
        $seriesInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SERIES);
        $seriesId = $seriesInfo->getAttributeId();
        $seriesAllOption = $this->prodCompHelper->getAttributeOptionAll($seriesId);
        foreach ($seriesAllOption as $key => $item) {
            $seriesArr[$item->getOptionId()] = $item->getValue();
        }
        //Comp Value
        $compValueInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_VALUE);
        $compValueId = $compValueInfo->getAttributeId();
        $compValueAllOption = $this->prodCompHelper->getAttributeOptionAll($compValueId);
        foreach ($compValueAllOption as $key => $item) {
            $compValueArr[$item->getOptionId()] = $item->getValue();
        }
        /**
         * For Filter Options[END]
         */
        $store = $this->_getStore();
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Id'),
                'html_name' => 'entity_id',
                'required' => true,
                'align' => 'center',
                'index' => 'entity_id',
            ]
        );
        if ($compType != 'Case') {
            $this->addColumn(
                'comp_socket',
                [
                    'header' => __('Socket'),
                    'index' => 'comp_socket',
                    'header_css_class' => 'col-type',
                    'column_css_class' => 'col-type',
                    'type' => 'options',
                    'options' => $socketArr,
                    'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer\CompSocket',
                ]
            );
        }
        $this->addColumn(
            'comp_manufacture',
            [
                'header' => __('Manufacturer'),
                'index' => 'comp_manufacture',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $manuArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer\CompManu',
            ]
        );
        $this->addColumn(
            'comp_model',
            [
                'header' => __('Model'),
                'index' => 'comp_model',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $modelArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer\CompModel',
            ]
        );
        if ($compType == 'CPU') {
            $this->addColumn(
                'comp_series',
                [
                    'header' => __('Series'),
                    'index' => 'comp_series',
                    'header_css_class' => 'col-type',
                    'column_css_class' => 'col-type',
                    'type' => 'options',
                    'options' => $seriesArr,
                    'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer\CompSeries',
                ]
            );
        }
        $this->addColumn(
            'comp_value',
            [
                'header' => __('Compatibility'),
                'index' => 'comp_value',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $compValueArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer\CompValue',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $status,
            ]
        );
        $this->addColumn(
            'pname',
            [
                'header' => __('Products'),
                'index' => 'pname',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'filter' => false,
            ]
        );
        $this->addColumn(
            'template_text',
            [
                'header' => __('Template Text'),
                'index' => 'template_text',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer\TemplateText',
                'filter' => false,
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/massAction/actiongrids', ['_current' => true]);
    }
}
