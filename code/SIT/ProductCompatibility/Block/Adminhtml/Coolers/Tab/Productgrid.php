<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\Coolers\Tab;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\Store;
use SIT\ProductCompatibility\Model\Product as ProductCompatibilityModel;

class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended {

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $coreRegistry = null;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\Block\Template\Context                                 $context              [description]
	 * @param \Magento\Backend\Helper\Data                                            $backendHelper        [description]
	 * @param \Magento\Catalog\Model\ProductFactory                                   $productFactory       [description]
	 * @param \Magento\Framework\Registry                                             $coreRegistry         [description]
	 * @param \Magento\Framework\Module\Manager                                       $moduleManager        [description]
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory          [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                              $storeManager         [description]
	 * @param ProductCompatibilityModel                                               $productCompatibility [description]
	 * @param Visibility|null                                                         $visibility           [description]
	 * @param array                                                                   $data                 [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		ProductCompatibilityModel $productCompatibility,
		Visibility $visibility = null,
		array $data = []
	) {
		$this->_storeManager = $storeManager;
		$this->productFactory = $productFactory;
		$this->coreRegistry = $coreRegistry;
		$this->productCompatibility = $productCompatibility;
		$this->_setsFactory = $setsFactory;
		$this->moduleManager = $moduleManager;
		$this->visibility = $visibility ?: ObjectManager::getInstance()->get(Visibility::class);
		parent::__construct($context, $backendHelper, $data);
	}

	/**
	 * [_construct description]
	 * @return [type] [description]
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('productcompatibility_grid_products');
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
				'sit_productcompatibility_productcompatibility_product',
				'position',
				'product_id=entity_id',
				'productcompatibility_id=' . $this->getRequest()->getParam("entity_id"),
				'left');
		} else {
			$collection->joinField('position',
				'sit_productcompatibility_productcompatibility_product',
				'position',
				'product_id=entity_id',
				'productcompatibility_id=0',
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
			'entity_id',
			[
				'header' => __('ID'),
				'width' => '50px',
				'index' => 'entity_id',
				'type' => 'number',
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
		$this->addColumn(
			'sku',
			[
				'header' => __('SKU'),
				'index' => 'sku',
				'header_css_class' => 'col-sku',
				'column_css_class' => 'col-sku',
			]
		);
		$store = $this->_getStore();
		$this->addColumn(
			'price',
			[
				'header' => __('Price'),
				'type' => 'price',
				'currency_code' => $store->getBaseCurrency()->getCode(),
				'index' => 'price',
				'header_css_class' => 'col-price',
				'column_css_class' => 'col-price',
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
		return $this->getUrl('*/coolers/grids', ['_current' => true]);
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
		$model = $this->productCompatibility->getCollection()->addFieldToFilter('productcompatibility_id', $id);
		$grids = [];
		foreach ($model as $key => $value) {
			$grids[] = $value->getProductId();
		}
		$prodId = [];
		foreach ($grids as $obj) {
			$prodId[$obj] = ['position' => "0"];
		}
		return $prodId;
	}
}
