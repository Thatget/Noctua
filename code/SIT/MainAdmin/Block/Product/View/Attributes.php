<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\MainAdmin\Block\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Catalog\Model\Product\Attribute\Repository;

class Attributes extends \Magento\Catalog\Block\Product\View\Attributes {
	/**
	 * @var Product
	 */
	protected $_product = null;

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var PriceCurrencyInterface
	 */
	protected $priceCurrency;

	/**
	 * @var \Magento\Eav\Model\Entity\Attribute\Group
	 */
	protected $eavGroup;

	/**
	 * @var Repository
	 */

	protected $productAttributeRepository;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\Template\Context $context                    [description]
	 * @param \Magento\Framework\Registry                      $registry                   [description]
	 * @param PriceCurrencyInterface                           $priceCurrency              [description]
	 * @param \Magento\Eav\Model\Entity\Attribute\Group        $eavGroup                   [description]
	 * @param Repository                                       $productAttributeRepository [description]
	 * @param array                                            $data                       [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $registry,
		PriceCurrencyInterface $priceCurrency,
		\Magento\Eav\Model\Entity\Attribute\Group $eavGroup,
		Repository $productAttributeRepository,
		array $data = []
	) {
		$this->eavGroup = $eavGroup;
		$this->productAttributeRepository = $productAttributeRepository;
		parent::__construct($context, $registry, $priceCurrency, $data);
	}

	/**
	 * @return Product
	 */
	public function getProduct() {
		if (!$this->_product) {
			$this->_product = $this->_coreRegistry->registry('product');
		}
		return $this->_product;
	}
	/**
	 * $excludeAttr is optional array of attribute codes to exclude them from additional data array
	 *
	 * @param array $excludeAttr
	 * @return array
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAdditionalData(array $excludeAttr = []) {
		$data = [];
		$product = $this->getProduct();
		$attributes = $product->getAttributes();
		foreach ($attributes as $attribute) {
			if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
				$value = $attribute->getFrontend()->getValue($product);

				if ($value instanceof Phrase) {
					$value = (string) $value;
				} elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
					$value = $this->priceCurrency->convertAndFormat($value);
				} elseif (!$attribute->getIsHtmlAllowedOnFront()) {
					$value = $this->escapeHtml($value);
				}
				$group = 0;
				if ($tmp = $attribute->getData('attribute_group_id')) {
					$group = $tmp;
				}
				if (is_string($value) && strlen($value)) {
					$data[$group]['items'][$attribute->getAttributeCode()] = array(
						'label' => __($attribute->getStoreLabel()),
						'value' => $value,
						'code' => $attribute->getAttributeCode(),
					);

					$data[$group]['attrid'] = $attribute->getId();
				}
			}
		}
		foreach ($data as $groupId => &$group) {
			$groupModel = $this->eavGroup->load($groupId);
			$group['title'] = $groupModel->getAttributeGroupName();
		}
		return $data;
	}

	public function getAttrData($attrCode) {
		$attrData = $this->productAttributeRepository->get($attrCode);
		$labelLink = $attrData->getLabelLink();
		return $labelLink;
	}
}
