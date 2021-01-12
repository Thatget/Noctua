<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Block\Adminhtml\Tab;

use Magento\Store\Model\Store;
use SIT\ProductReviewNew\Model\Product as ProductReviewModel;

class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended {

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $coreRegistry = null;

	/**
	 * @var \Magento\Framework\Module\Manager
	 */
	protected $moduleManager;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var ProductReviewModel
	 */
	protected $reviewModel;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\Block\Template\Context    $context        [description]
	 * @param \Magento\Backend\Helper\Data               $backendHelper  [description]
	 * @param \Magento\Catalog\Model\ProductFactory      $productFactory [description]
	 * @param \Magento\Framework\Registry                $coreRegistry   [description]
	 * @param \Magento\Framework\Module\Manager          $moduleManager  [description]
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager   [description]
	 * @param ProductReviewModel                         $reviewModel    [description]
	 * @param array                                      $data           [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		ProductReviewModel $reviewModel,
		array $data = []
	) {
		$this->productFactory = $productFactory;
		$this->coreRegistry = $coreRegistry;
		$this->moduleManager = $moduleManager;
		$this->_storeManager = $storeManager;
		$this->reviewModel = $reviewModel;
		parent::__construct($context, $backendHelper, $data);
	}

	/**
	 * [_construct description]
	 * @return [type] [description]
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('productreview_grid_products');
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
		$store = $this->_getStore();
		$collection = $this->productFactory->create()->getCollection()->addAttributeToSelect(
			'sku'
		)->addAttributeToSelect(
			'name'
		)->addAttributeToSelect(
			'attribute_set_id'
		)->addAttributeToSelect(
			'type_id'
		)->setStore(
			$store
		);

		if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
			$collection->joinField(
				'qty',
				'cataloginventory_stock_item',
				'qty',
				'product_id=entity_id',
				'{{table}}.stock_id=1',
				'left'
			);
		}
		if ($store->getId()) {
			$collection->setStoreId($store->getId());
			$collection->addStoreFilter($store);
			$collection->joinAttribute(
				'name',
				'catalog_product/name',
				'entity_id',
				null,
				'inner',
				Store::DEFAULT_STORE_ID
			);
			$collection->joinAttribute(
				'status',
				'catalog_product/status',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute(
				'visibility',
				'catalog_product/visibility',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
		} else {
			$collection->addAttributeToSelect('price');
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
		}
		if ($this->getRequest()->getParam("entity_id")) {
			$collection->joinField('position',
				'sit_productreviewnew_productreview_product',
				'position',
				'product_id=entity_id',
				'productreview_id=' . $this->getRequest()->getParam("entity_id"),
				'left');
		} else {
			$collection->joinField('position',
				'sit_productreviewnew_productreview_product',
				'position',
				'product_id=entity_id',
				'productreview_id=0',
				'left');
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * @param Column $column
	 * @return $this
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
			'name',
			[
				'header' => __('Name'),
				'index' => 'name',
				'header_css_class' => 'col-type',
				'column_css_class' => 'col-type',
			]
		);

		$this->addColumn('categories',
			[
				'header' => __('Category'),
				'index' => 'categories',
				'header_css_class' => 'col-type',
				'column_css_class' => 'col-type',
				'renderer' => 'SIT\ProductReviewNew\Block\Adminhtml\ProductReview\Renderer\CategoryList',
				'filter_condition_callback' => [$this, '_filterStoreCondition'],
				'sortable' => false,
			]
		);

		$this->addColumn(
			'position',
			[
				'header' => __('Position'),
				'name' => 'position',
				'width' => 60,
				'type' => 'number',
				'validate_class' => 'validate-number',
				'index' => 'position',
				'editable' => true,
				'edit_only' => true,
			]
		);
		return parent::_prepareColumns();
	}

	/**
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/productreview/grids', ['_current' => true]);
	}

	/**
	 * @return array
	 */
	protected function _getSelectedProducts() {
		$products = array_keys($this->getSelectedProducts());
		return $products;
	}

	/**
	 * @return array
	 */
	public function getSelectedProducts() {
		$id = $this->getRequest()->getParam('entity_id');
		$reviewData = $this->reviewModel->getCollection()->addFieldToFilter('productreview_id', $id);
		$grids = [];
		foreach ($reviewData as $key => $value) {
			$grids[] = $value->getProductId();
		}
		$prodId = [];
		foreach ($grids as $obj) {
			$prodId[$obj] = ['position' => "0"];
		}
		return $prodId;
	}
}
