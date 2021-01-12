<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [20-06-2019]
 */
namespace SIT\ProductReviewNew\Block\Adminhtml\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use SIT\ProductReviewNew\Model\Product as SitReviewProduct;
use SIT\ProductReviewNew\Model\ProductReviewFactory;

class Reviewgrid extends Extended
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
     * @var ProductReviewFactory
     */
    protected $_productreviewnewFactory;

    /**
     * @var SitReviewProduct
     */
    protected $sitReviewProduct;

    /**
     * [__construct description]
     * @param Context               $context              [description]
     * @param Data                  $backendHelper        [description]
     * @param Registry              $coreRegistry         [description]
     * @param Manager               $moduleManager        [description]
     * @param CollectionFactory     $setsFactory          [description]
     * @param StoreManagerInterface $storeManager         [description]
     * @param ProductReviewFactory     $productreviewnewFactory [description]
     * @param SitReviewProduct         $sitReviewProduct        [description]
     * @param array                 $data                 [description]
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $coreRegistry,
        Manager $moduleManager,
        CollectionFactory $setsFactory,
        StoreManagerInterface $storeManager,
        ProductReviewFactory $productreviewnewFactory,
        SitReviewProduct $sitReviewProduct,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->moduleManager = $moduleManager;
        $this->_setsFactory = $setsFactory;
        $this->_storeManager = $storeManager;
        $this->_productreviewnewFactory = $productreviewnewFactory;
        $this->sitReviewProduct = $sitReviewProduct;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * [_construct description]
     * @return [type] [description]
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productreview_grid_products');
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
        $collection = $this->_productreviewnewFactory->create()->getCollection()->addAttributeToSelect('*');
        if ($entity_id) {
            $collection->joinField('position',
                'sit_productreviewnew_productreview_product',
                'position',
                'productreview_id=entity_id',
                'product_id=' . $entity_id,
                'left');
        } else {
            $collection->joinField('position',
                'sit_productreviewnew_productreview_product',
                'position',
                'productreview_id=entity_id',
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
        $status = [1 => 'Enabled', 0 => 'Disabled'];
        $store = $this->_getStore();
        $collection = $this->_productreviewnewFactory->create()->getCollection()->addAttributeToSelect('*');

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
            'review_website',
            [
                'header' => __('Website'),
                'index' => 'review_website',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
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
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => 'sit_productreviewnew/productreview/edit/'],
                        'field' => 'entity_id',
                        'target' => '_blank',
                        'class' => 'sit-admin-anchor-button',
                    ],
                ],
                'filter' => false,
                'is_system' => true,
                'sortable' => false,
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sit_productreviewnew/productreview/reviewgrids', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $slides = $this->getSelectedReview();
        return $slides;
    }

    /**
     * @return array
     */
    public function getSelectedReview()
    {
        $entity_id = $this->getRequest()->getParam('id');
        $tempSitReviewProduct = $this->sitReviewProduct;
        $collection = $tempSitReviewProduct->getCollection()->addFieldToSelect('productreview_id')->addFieldToFilter("product_id", ["eq" => $entity_id]);
        $reviewIds = [];
        foreach ($collection as $key => $value) {
            $reviewIds[] = $value['productreview_id'];
        }
        return $reviewIds;
    }
}
