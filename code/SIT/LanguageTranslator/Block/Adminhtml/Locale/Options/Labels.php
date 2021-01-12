<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\LanguageTranslator\Block\Adminhtml\Locale\Options;

/**
 * Attribute add/edit form options tab
 *
 * @api
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Labels extends \Magento\Backend\Block\Template
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_registry;
	protected $request;
	protected $translation;
	/**
	 * @var string
	 */
	protected $_template = 'SIT_LanguageTranslator::locale/options/labels.phtml';

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param array $data
	 * @codeCoverageIgnore
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\App\RequestInterface $request,
		\SIT\LanguageTranslator\Model\ResourceModel\Tranlation $translation,
		\Magento\Framework\Registry $registry,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->request = $request;
		$this->translation = $translation;
		$this->_registry = $registry;
	}

	/**
	 * Retrieve stores collection with default store
	 *
	 * @return \Magento\Store\Model\ResourceModel\Store\Collection
	 */
	public function getStores()
	{
		if (!$this->hasStores()) {
			$stores = $this->_storeManager->getStores();
			// uasort(
			// 	$stores,
			// 	function (\Magento\Store\Model\Store $storeA, \Magento\Store\Model\Store $storeB) {
			// 		return $storeA->getSortOrder() <=> $storeB->getSortOrder();
			// 	}
			// );
			$this->setData('stores', $stores);
		}

		return $this->_getData('stores');
	}

	/**
	 * Retrieve frontend labels of attribute for each store
	 *
	 * @return array
	 */
	public function getLabelValues()
	{
		$values = (array) $this->getAttributeObject()->getFrontend()->getLabel();
		$storeLabels = $this->getAttributeObject()->getStoreLabels();
		foreach ($this->getStores() as $store) {
			if ($store->getId() != 0) {
				$values[$store->getId()] = isset($storeLabels[$store->getId()]) ? $storeLabels[$store->getId()] : '';
			}
		}
		return $values;
	}

	/**
	 * Retrieve attribute object from registry
	 *
	 * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
	 * @codeCoverageIgnore
	 */
	private function getAttributeObject()
	{
		return $this->_registry->registry('entity_attribute');
	}
	public function getPost()
	{
		$id = $this->request->getParam('translation_id');
		return $this->translation->loadStore($id);
	}
	public function getParams()
	{
		return $id = $this->request->getParam('translation_id');
	}
}
