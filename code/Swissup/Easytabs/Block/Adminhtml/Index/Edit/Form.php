<?php
namespace Swissup\Easytabs\Block\Adminhtml\Index\Edit;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;

/**
 * Adminhtml easytab edit form main tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic {
	/**
	 * @var \Magento\Store\Model\System\Store
	 */
	protected $systemStore;
	/**
	 * @var \Swissup\Easytabs\Model\TabsFactory
	 */
	protected $tabsOptionsFactory;

	/**
	 * @var CollectionFactory
	 */
	protected $pageCollection;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Magento\Store\Model\System\Store $systemStore
	 * @param \Swissup\Easytabs\Model\TabsFactory $tabsOptionsFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Store\Model\System\Store $systemStore,
		CollectionFactory $pageCollection,
		\Swissup\Easytabs\Model\TabsFactory $tabsOptionsFactory,
		array $data = []
	) {
		$this->pageCollection = $pageCollection;
		$this->systemStore = $systemStore;
		$this->tabsOptionsFactory = $tabsOptionsFactory;
		parent::__construct($context, $registry, $formFactory, $data);
	}
	/**
	 * Init form
	 *
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('easytab_form');
		$this->setTitle(__('Tab Information'));
	}
	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm() {

		/** @var \Swissup\Easytabs\Model\Data $model */
		$model = $this->_coreRegistry->registry('easytab');

		/*
			         * Checking if user have permissions to save information
		*/
		if ($this->_isAllowedAction('Swissup_Easytabs::save')) {
			$isElementDisabled = false;
		} else {
			$isElementDisabled = true;
		}

		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create(
			[
				'data' => [
					'id' => 'edit_form',
					'action' => $this->getData('action'),
					'method' => 'post',
					'enctype' => 'multipart/form-data',
				],
			]
		);

		$form->setHtmlIdPrefix('easytab_');

		$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Tab Information')]);

		if ($model->getTabId()) {
			$fieldset->addField('tab_id', 'hidden', ['name' => 'tab_id']);
		} else {
			// set new tab enabled by default
			$model->setStatus(1);
		}

		$fieldset->addField(
			'status',
			'select',
			[
				'label' => __('Status'),
				'title' => __('Status'),
				'name' => 'status',
				'required' => true,
				'options' => $model->getAvailableStatuses(),
				'disabled' => $isElementDisabled,
			]
		);

		$fieldset->addField(
			'title',
			'text',
			[
				'name' => 'title',
				'label' => __('Title'),
				'title' => __('title'),
				'required' => true,
				'disabled' => $isElementDisabled,
			]
		);

		$fieldset->addField(
			'alias',
			'text',
			[
				'name' => 'alias',
				'label' => __('Alias'),
				'title' => __('Alias'),
				'required' => true,
				'disabled' => $isElementDisabled,
			]
		);

		$block = $model->getBlock();
		$blockTypes = $this->_getBlockTypes();
		if (!isset($blockTypes[$block])) {
			$model->setBlock('Swissup\Easytabs\Block\Tab\Html');
		}
		$model->setBlockType($model->getBlock());

		$fieldset->addField(
			'block_type',
			'select',
			[
				'name' => 'block_type',
				'label' => __('Block Type'),
				'title' => __('Block Type'),
				'required' => true,
				'options' => $this->_getBlockTypes(),
				'disabled' => $isElementDisabled,
				'after_element_html' => $this->_getWidgetSelectAfterHtml(),
			]
		);

		$fieldset->addField(
			'block',
			'text',
			[
				'name' => 'block',
				'label' => __('Block'),
				'title' => __('Block'),
				'required' => true,
				'disabled' => true,
			]
		);

		$sortOrder = $model->getSortOrder();
		if (empty($sortOrder)) {
			$model->setSortOrder(0);
		}
		$fieldset->addField(
			'sort_order',
			'text',
			[
				'name' => 'sort_order',
				'label' => __('Sort Order'),
				'title' => __('Sort Order'),
				'required' => true,
				'disabled' => $isElementDisabled,
				'note' => 'Note : Set priority of tab Ex. 0 is higher priority',
			]
		);
		$fieldset->addField(
			'parent_tab',
			'text',
			[
				'label' => __('Parent Tab'),
				'title' => __('parent_tab'),
				'name' => 'parent_tab',
				'disabled' => $isElementDisabled,
				'note' => 'Note : Add multiple parent tab alias by ","(comma) seperated',
			]
		);

		$visible_tab = $fieldset->addField(
			'visible_tab',
			'select',
			[
				'label' => __('Visible Tab'),
				'title' => __('visible_tab'),
				'name' => 'visible_tab',
				'options' => $this->_getVisibleTabOptions(),
			]
		);

		$remove_from_category = $fieldset->addField(
			'remove_from_category',
			'text',
			[
				'label' => __('Remove From'),
				'title' => __('remove_from_category'),
				'name' => 'remove_from_category',
				'note' => 'Note : Add multiple category by ","(comma) seperated',
			]
		);

		$remove_from_product = $fieldset->addField(
			'remove_from_product',
			'text',
			[
				'label' => __('Remove From'),
				'title' => __('remove_from_product'),
				'name' => 'remove_from_product',
				'note' => 'Note : Add multiple product by ","(comma) seperated',
			]
		);

		$product_key = $fieldset->addField(
			'product_key',
			'text',
			[
				'label' => __('Set Product Sku'),
				'title' => __('product_key'),
				'name' => 'product_key',
				'required' => true,
				'note' => 'Note : For product -> set product sku, Multiple value with "," separated',
			]
		);

		$category_key = $fieldset->addField(
			'category_key',
			'text',
			[
				'label' => __('Set Category Url Key'),
				'title' => __('category_key'),
				'name' => 'category_key',
				'required' => true,
				'note' => 'Note : For category -> set url key, Multiple value with "," separated',
			]
		);

		$cms_pages = $fieldset->addField(
			'cms_pages',
			'multiselect',
			[
				'label' => __('Select CMS Pages'),
				'title' => __('cms_pages'),
				'name' => 'cms_pages',
				'required' => true,
				'values' => $this->_getCmsPageOptions(),
			]
		);

		$custom_page = $fieldset->addField(
			'custom_page',
			'text',
			[
				'label' => __('Set Custom Page'),
				'title' => __('custom_page'),
				'name' => 'custom_page',
				'required' => true,
				'note' => 'Note : Set custom page like "front_name/controller/action"',
			]
		);

		/* Check is single store mode */
		if (!$this->_storeManager->isSingleStoreMode()) {
			$field = $fieldset->addField(
				'store_id',
				'multiselect',
				[
					'name' => 'stores[]',
					'label' => __('Store View'),
					'title' => __('Store View'),
					'required' => true,
					'values' => $this->systemStore->getStoreValuesForForm(false, true),
					'disabled' => $isElementDisabled,
				]
			);
			$renderer = $this->getLayout()->createBlock(
				'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
			);
			$field->setRenderer($renderer);
		} else {
			$fieldset->addField(
				'store_id',
				'hidden',
				['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
			);
			$model->setStoreId($this->_storeManager->getStore(true)->getId());
		}

		// add hidden field `widget_tab`
		// '0' - product tab
		// '1' - wiidget tab
		// values set in layout for parent block
		$fieldset->addField(
			'widget_tab',
			'hidden',
			['name' => 'widget_tab']
		);

		$this->setChild(
			'form_after',
			$this->getLayout()->createBlock(
				'Magento\Backend\Block\Widget\Form\Element\Dependence'
			)
				->addFieldMap($visible_tab->getHtmlId(), $visible_tab->getName())
				->addFieldMap($remove_from_category->getHtmlId(), $remove_from_category->getName())
				->addFieldMap($remove_from_product->getHtmlId(), $remove_from_product->getName())
				->addFieldMap($product_key->getHtmlId(), $product_key->getName())
				->addFieldMap($category_key->getHtmlId(), $category_key->getName())
				->addFieldMap($cms_pages->getHtmlId(), $cms_pages->getName())
				->addFieldMap($custom_page->getHtmlId(), $custom_page->getName())

				->addFieldDependence($remove_from_product->getName(), $visible_tab->getName(), 'products')
				->addFieldDependence($remove_from_category->getName(), $visible_tab->getName(), 'category')
				->addFieldDependence($product_key->getName(), $visible_tab->getName(), 'specific_product')
				->addFieldDependence($category_key->getName(), $visible_tab->getName(), 'specific_category')
				->addFieldDependence($cms_pages->getName(), $visible_tab->getName(), 'cms_page')
				->addFieldDependence($custom_page->getName(), $visible_tab->getName(), 'custom_page')
		);

		$model->setWidgetTab($this->getParentBlock()->getWidgetTab());

		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
	/**
	 * Get Easy Tabs block types as array
	 * @return Array
	 */
	protected function _getBlockTypes() {
		$showProductTabs = !$this->getParentBlock()->getWidgetTab();
		$tabs = $this->tabsOptionsFactory->create()->getTabsArray();
		$types = [];
		foreach ($tabs as $tab) {
			if (in_array($tab['code'], ['easytabs_cms', 'easytabs_template', 'easytabs_html'])
				|| $showProductTabs) {
				$types[$tab['type']] = $tab['name'];
			}
		}
		return $types;
	}
	/**
	 * Prepare widgets select after element HTML
	 *
	 * @return string
	 */
	protected function _getWidgetSelectAfterHtml() {
		$html = '<p class="nm"><small></small></p>';
		$i = 0;
		foreach ($this->_getAvailableWidgets(true) as $data) {
			$html .= sprintf('<div id="widget-description-%s" class="no-display">%s</div>', $i, $data['description']);
			$i++;
		}
		return $html;
	}
	/**
	 * Return array of available widgets based on configuration
	 *
	 * @param bool $withEmptyElement
	 * @return array
	 */
	protected function _getAvailableWidgets($withEmptyElement = false) {
		if (!$this->hasData('available_widgets')) {
			$result = [];
			$allWidgets = $this->tabsOptionsFactory->create()->getTabsArray();
			foreach ($allWidgets as $widget) {
				$result[] = $widget;
			}
			if ($withEmptyElement) {
				array_unshift($result, ['type' => '', 'name' => __('-- Please Select --'), 'description' => '']);
			}
			$this->setData('available_widgets', $result);
		}

		return $this->_getData('available_widgets');
	}
	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId
	 * @return bool
	 */
	protected function _isAllowedAction($resourceId) {
		return $this->_authorization->isAllowed($resourceId);
	}

	/**
	 * Set visible_tab value in select box
	 * @return array visible_tabs
	 */
	protected function _getVisibleTabOptions() {
		$visibleTab = [
			'' => '---Please Select---',
			'products' => 'All Products',
			'category' => 'All Category',
			'specific_product' => 'Specific Product',
			'specific_category' => 'Specific Category',
			'cms_page' => 'CMS Pages',
			'custom_page' => 'Custom Page',
		];
		return $visibleTab;
	}

	/**
	 * Set cms page values in select box.
	 * @return array
	 */
	protected function _getCmsPageOptions() {
		$collection = $this->pageCollection->create()->addFieldToSelect('title')->addFieldToSelect('identifier');
		$collection->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);
		$collection->getSelect()->group('identifier');
		$cms_pages = [];

		foreach ($collection as $values) {
			$cms_pages[] = ['value' => $values->getIdentifier(), 'label' => $values->getTitle()];
		}
		return $cms_pages;
	}
}
