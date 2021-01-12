<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [21-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\Product as SitCompProduct;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory as CollectionCompFactory;

class Compatibilitygrid extends Extended
{
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
     * @var SitCompProduct
     */
    protected $sitCompProduct;

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
     * @param SitCompProduct                                     $sitCompProduct        [description]
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
        SitCompProduct $sitCompProduct,
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
        $this->sitCompProduct = $sitCompProduct;
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
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_products' => 1]);
        } else {
            $this->setDefaultFilter(['in_products' => 0]);
        }
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
        $entity_id = $this->getRequest()->getParam('id');
        $store = $this->_getStore();
        $collection = $this->collectionCompFactory->create()->addFieldToSelect('*');
        if ($entity_id) {
            $collection->joinField('position',
                'sit_productcompatibility_productcompatibility_product',
                'position',
                'productcompatibility_id=entity_id',
                'product_id=' . $entity_id,
                'left');
        } else {
            $collection->joinField('position',
                'sit_productcompatibility_productcompatibility_product',
                'position',
                'productcompatibility_id=entity_id',
                'product_id=0',
                'left');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
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
        //Comp Type
        $compTypeInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
        $compTypeId = $compTypeInfo->getAttributeId();
        $compTypeAllOption = $this->prodCompHelper->getAttributeOptionAll($compTypeId);
        foreach ($compTypeAllOption as $key => $item) {
            $compTypeArr[$item->getOptionId()] = $item->getValue();
        }
        /**
         * For Filter Options[END]
         */
        $store = $this->_getStore();
        $this->addColumn(
            'in_products',
            [
                'type' => 'checkbox',
                'html_name' => 'products_id',
                'required' => true,
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id',
            ]
        );
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

        $this->addColumn(
            'comp_socket',
            [
                'header' => __('Socket'),
                'index' => 'comp_socket',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $socketArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer\CompSocket',
            ]
        );

        $this->addColumn(
            'comp_manufacture',
            [
                'header' => __('Manufacturer'),
                'index' => 'comp_manufacture',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $manuArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer\CompManu',
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
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer\CompModel',
            ]
        );

        $this->addColumn(
            'comp_series',
            [
                'header' => __('Series'),
                'index' => 'comp_series',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $seriesArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer\CompSeries',
            ]
        );

        $this->addColumn(
            'comp_value',
            [
                'header' => __('Compatibility'),
                'index' => 'comp_value',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $compValueArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer\CompValue',
            ]
        );

        $this->addColumn(
            'comp_type',
            [
                'header' => __('Type'),
                'index' => 'comp_type',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'type' => 'options',
                'options' => $compTypeArr,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer\CompType',
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
            'position',
            [
                'header' => __('Position'),
                'name' => 'position',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => true,
                'edit_only' => true,
            ]
        );
        $this->addColumn(
            'actions',
            [
                'header' => __('Action'),
                'type' => 'action',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'SIT\ProductCompatibility\Block\Adminhtml\Catalog\Tab\Renderer\Actions',
            ]);
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sit_productcompatibility/catalog/compgrids', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $slides = $this->getSelectedComp();
        return $slides;
    }

    /**
     * @return array
     */
    public function getSelectedComp()
    {
        $entity_id = $this->getRequest()->getParam('id');
        $tempSitCompProduct = $this->sitCompProduct;
        $collection = $tempSitCompProduct->getCollection()->addFieldToSelect('productcompatibility_id')->addFieldToFilter("product_id", ["eq" => $entity_id]);
        $compIds = [];
        foreach ($collection as $key => $value) {
            $compIds[] = $value['productcompatibility_id'];
        }
        return $compIds;
    }
}
