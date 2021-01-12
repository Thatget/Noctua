<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\App\DeploymentConfig\Reader;

class AttrOptions extends Template {

	/**
	 * @var \Magento\Eav\Model\Entity\Attribute
	 */
	protected $entityAttribute;

	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
	 */
	protected $attributeOptionCollectionFactory;

	/**
	 * @var \SIT\ProductCompatibility\Helper\Data
	 */
	protected $prodCompHelper;
	/**
	 * @var Reader
	 */
	protected $_configReader;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\Block\Template\Context                                    $context                          [description]
	 * @param \Magento\Eav\Model\Entity\Attribute                                        $entityAttribute                  [description]
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory [description]
	 * @param \SIT\ProductCompatibility\Helper\Data                                      $prodCompHelper                   [description]
	 * @param Reader                                                                     $reader                           [description]
	 * @param array                                                                      $data                             [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Eav\Model\Entity\Attribute $entityAttribute,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory,
		\SIT\ProductCompatibility\Helper\Data $prodCompHelper,
		Reader $reader,
		array $data = []
	) {
		$this->entityAttribute = $entityAttribute;
		$this->attributeOptionCollectionFactory = $attributeOptionCollectionFactory;
		$this->prodCompHelper = $prodCompHelper;
		$this->_configReader = $reader;
		parent::__construct($context, $data);
	}

	public function getAttrOptions($attrCode) {
		$entityAttribute = $this->entityAttribute->loadByCode('sit_productcompatibility_productcompatibility', $attrCode);
		$attributeId = $entityAttribute->getAttributeId();
		$getAllOptions = $this->prodCompHelper->getAttributeOptionAll($attributeId);
		$attrOptions = [];
		$count = 0;
		foreach ($getAllOptions as $key => $value) {
			$attrOptions[$count]['label'] = $value['option_id'];
			$attrOptions[$count]['value'] = $value['value'];
			$count++;
		}
		return $attrOptions;
	}

	public function getAdminBaseUrl() {
		$config = $this->_configReader->load();
		$adminSuffix = $config['backend']['frontName'];
		return $this->getBaseUrl() . $adminSuffix . '/';
	}
}
