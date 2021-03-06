<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\ProSliderCustom\Block\Adminhtml\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mediarocks\ProSlider\Model\SlideFactory;
use Mediarocks\ProSlider\Model\SliderFactory;
use Magento\Framework\Json\DecoderInterface;

class Slidegrid extends Extended
{
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
     * @var SlideFactory
     */
    protected $slideFactory;

    /**
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * [__construct description]
     * @param Context               $context       [description]
     * @param Data                  $backendHelper [description]
     * @param Registry              $coreRegistry  [description]
     * @param Manager               $moduleManager [description]
     * @param CollectionFactory     $setsFactory   [description]
     * @param StoreManagerInterface $storeManager  [description]
     * @param SlideFactory          $slideFactory  [description]
     * @param SliderFactory         $sliderFactory [description]
     * @param DecoderInterface      $jsonDecoder [description]
     * @param array                 $data          [description]
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $coreRegistry,
        Manager $moduleManager,
        CollectionFactory $setsFactory,
        StoreManagerInterface $storeManager,
        SlideFactory $slideFactory,
        SliderFactory $sliderFactory,
        DecoderInterface $jsonDecoder,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->moduleManager = $moduleManager;
        $this->_setsFactory = $setsFactory;
        $this->_storeManager = $storeManager;
        $this->slideFactory = $slideFactory;
        $this->sliderFactory = $sliderFactory;
        $this->jsonDecoder = $jsonDecoder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * [_construct description]
     * @return [type] [description]
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('producttech_grid_products');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('entity_id')) {
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
    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * [prepareCollection for grid]
     * @return collection
     */
    protected function _prepareCollection() {
        $entity_id = $this->getRequest()->getParam('entity_id');

        $store = $this->_getStore();

        $collection = $this->slideFactory->create()->getCollection()->addAttributeToSelect('*');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @param Column $column
     * @return \Mediarocks\ProSlider\Block\Adminhtml\Tab\Slidegrid
     */
    protected function _addColumnFilterToCollection($column) {
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
    protected function _prepareColumns() {

        $store = $this->_getStore();
        $collection = $this->slideFactory->create()->getCollection()->addAttributeToSelect('*');

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
                'header' => __('id'),
                'html_name' => 'entity_id',
                'required' => true,
                'align' => 'center',
                'index' => 'entity_id',
            ]
        );

        $this->addColumn(
            'image',
            [
                'header' => __('Slide Image'),
                'index' => 'image',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'renderer' => 'Mediarocks\ProSlider\Block\Adminhtml\Tab\Image',
            ]
        );

        $this->addColumn(
            'slide_name',
            [
                'header' => __('Slide Name'),
                'index' => 'slide_name',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Slide Title'),
                'index' => 'title',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Slide Title'),
                'index' => 'title',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header' => __('Slide Status'),
                'index' => 'is_active',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
            ]
        );

        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'html_name' => 'sort_order',
                'align' => 'center',
                'index' => 'sort_order',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'sortable' => false,
                'filter' => false,
                'search' => false,
                'editable' => true,
                'edit_only' => true,
                'renderer' => 'Qsoft\ProSliderCustom\Block\Adminhtml\Tab\SortOrder',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/grids', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts() {
        $slides = $this->getSelectedSlides();
        return $slides;
    }

    /**
     * @return array
     */
    public function getSelectedSlides() {
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->sliderFactory->create()->load($id);
        $result = [];
        if ($model['slides']) {
            if (!json_decode($model['slides'])) {
                $slidesArr = explode(",", $model['slides']);
                if (is_array($slidesArr)) {
                    foreach($slidesArr as $slide) {
                        $result[] = (int)$slide;
                    }
                }
            } else {
                $slides = $this->jsonDecoder->decode($model['slides']);

                if (is_array($slides)) {
                    foreach ($slides as $key => $slide) {
                        $result[] = $key;
                    }
                } else {
                    $result[] = $slides;
                }
            }
        }
        return $result;
//        return explode(',', $model['slides']);
    }
}