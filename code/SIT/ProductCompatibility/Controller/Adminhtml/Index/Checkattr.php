<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Checkattr extends Action {

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $_resultJsonFactory;

	/**
	 * @var \Magento\Eav\Setup\EavSetupFactory
	 */
	protected $eavSetupFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute
	 */
	protected $attributeFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context                         $context           [description]
	 * @param \Magento\Framework\Controller\Result\JsonFactory            $resultJsonFactory [description]
	 * @param \Magento\Eav\Setup\EavSetupFactory                          $eavSetupFactory   [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager      [description]
	 * @param \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory  [description]
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory
	) {
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->eavSetupFactory = $eavSetupFactory;
		$this->storeManager = $storeManager;
		$this->attributeFactory = $attributeFactory;
		parent::__construct($context);
	}

	/**
	 * Index action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute() {
		$attr_code = $this->getRequest()->getParam('attr_code');
		$attr_label = $this->getRequest()->getParam('attr_label');

		$attribute_arr = [$attr_label];
		$attributeInfo = $this->attributeFactory->getCollection()
			->addFieldToFilter('attribute_code', ['eq' => $attr_code])
			->getFirstItem();
		$option = [];
		if ($attributeInfo->getAttributeId()) {
			$option['attribute_id'] = $attributeInfo->getAttributeId();
			$option['value'][$attr_label][0] = $attr_label;
		}
		if (!empty($option)) {
			$eavSetup = $this->eavSetupFactory->create();
			$eavSetup->addAttributeOption($option);
		}
	}

	protected function _isAllowed() {
		return true;
	}
}