<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Block\Route;

use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;

class Compatibility extends \Magento\Framework\View\Element\Template {
	/**
	 * product compatiibility product table
	 */
	const PRODUCT_TABLE = 'sit_productcompatibility_productcompatibility_product';

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var ProductCompHelper
	 */
	protected $prodCompHelper;

	/**
	 * @var \SIT\ProductCompatibility\Model\ProductCompatibilityFactory
	 */
	protected $productCompFactory;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * @var \SIT\ProductCompatibility\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $sitProCollFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @var \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory
	 */
	protected $templatetextFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\Template\Context                                     $context             [description]
	 * @param \Magento\Framework\View\Result\PageFactory                                           $resultPageFactory   [description]
	 * @param ProductCompHelper                                                                    $prodCompHelper      [description]
	 * @param \SIT\ProductCompatibility\Model\ProductCompatibilityFactory                          $productCompFactory  [description]
	 * @param \Magento\Catalog\Model\ProductFactory                                                $productFactory      [description]
	 * @param \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $sitProCollFactory   [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                                           $storeManager        [description]
	 * @param \Magento\Framework\Json\Helper\Data                                                  $jsonHelper          [description]
	 * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory                 $templatetextFactory [description]
	 * @param array                                                                                $data                [description]
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		ProductCompHelper $prodCompHelper,
		\SIT\ProductCompatibility\Model\ProductCompatibilityFactory $productCompFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $sitProCollFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory,
		array $data = []
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->prodCompHelper = $prodCompHelper;
		$this->productCompFactory = $productCompFactory;
		$this->productFactory = $productFactory;
		$this->sitProCollFactory = $sitProCollFactory;
		$this->storeManager = $storeManager;
		$this->jsonHelper = $jsonHelper;
		$this->templatetextFactory = $templatetextFactory;
		parent::__construct($context, $data);
	}

	/**
	 * Return data from custom router call
	 *
	 * @return array
	 */
	public function getRouteCompatibilityData($compType, $urlKey) {
		$storeId = $this->storeManager->getStore()->getId();
		$productData = $this->sitProCollFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('url_key', ['eq' => $urlKey]);
		$productCompInstance = $productData->getFirstItem();
		$max_cooler_array = [];
		foreach ($productData as $key => $value) {
			$max_cooler_array[] = $value->getMaxCoolerHeight();
		}
		$maxCoolerSize = '';
		if ($compType == "Case") {
			$maxCoolerSize = max($max_cooler_array);
		}
		$model_id = $productCompInstance->getCompModel();
		$collection = $this->productCompFactory->create()->getCollection()
			->setStoreId($storeId)
			->addAttributeToSelect('*')
			->addAttributeToFilter('comp_model', ['eq' => $model_id])
			->addAttributeToFilter('status', ['eq' => 1]);

		$collection->getSelect()->joinLeft(
			['productTable' => self::PRODUCT_TABLE],
			'productTable.productcompatibility_id = e.entity_id ',
			[
				'productTable.product_id',
				'product_ids' => 'GROUP_CONCAT(productTable.product_id)',
			]
		)->where('productTable.productcompatibility_id = e.entity_id')->group("e.entity_id");
		$compatibility_array = [];
		$count = 0;
		// Changed by Adam: Fix issue Import / Export of comp list. Dupplicate product in cpu compatibility
		$productIdArr = [];
		foreach ($collection as $key => $value) {
			$finalTemplateText = '';
			$finalTemplateText = $this->getTemplateText($value->getTemplateText1(), $value->getTemplateText2(), $value->getTemplateText3(), $value->getTemplateText4());
			$extra_comment = $value->getCompExtraComment();
			$compatiibility = $value->getCompValue();
			$AttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_VALUE);
			$AttrID = $AttrInfo->getAttributeId();
			$AttrOptionLabel = $this->prodCompHelper->getAttrOptionLabel($AttrID, $compatiibility)->getFirstItem();
			$attr_label = $AttrOptionLabel['value'];
			$product_ids = explode(',', $value['product_ids']);
		
			// Changed by Adam: Fix issue Import / Export of comp list. Dupplicate product in cpu compatibility
			$productIds = [];
			$uniqueProductIds = [];
			if (is_array($product_ids)) {
				if (sizeof($productIdArr)) {
					foreach($product_ids as $productId) {
						if(!in_array($productId, $productIdArr)) {
							$uniqueProductIds[] = $productId;
						}
					}
					$product_ids = $uniqueProductIds;
					$productIdArr = array_merge($productIdArr, $product_ids);
				} 
				else {
					$productIdArr = array_merge($productIdArr, $product_ids);
				}
			}
			
			if(sizeof($product_ids)) {
				$products = $this->productFactory->create()->getCollection()->AddAttributeToSelect(['name', 'product_url', 'height_with_fan', 'height_with_fan_url'])->addAttributeToFilter('entity_id', ['in', $product_ids])->setOrder('name', 'ASC');
				foreach ($products as $keyPro => $product) {
					$compatibility = '';
					$compatibility_array[$count]['main'] = $key;
					$compatibility_array[$count]['product_id'] = $product->getId();
					$compatibility_array[$count]['product_name'] = $product->getName();
					$compatibility_array[$count]['product_url'] = $product->getProductUrl();
					$compatibility_array[$count]['height_with_fan'] = $product->getHeightWithFan();
					$compatibility_array[$count]['template_text_comment'] = $finalTemplateText;
					$compatibility_array[$count]['comp_extra_comment'] = $extra_comment;
					if ($compType == "Case") {
						$compatibility_array[$count]['height_with_fan_url'] = $product->getHeightWithFanUrl();
					}
					if ($compType == "CPU") {
						$compatibility_array[$count]['height_with_fan_url'] = $product->getHeightWithFanUrl();
						if ($attr_label == ProductCompHelper::COMP_TYPE_COMPATIBLE && trim($finalTemplateText) == '') {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'g1.png');
						} elseif ($attr_label == ProductCompHelper::COMP_TYPE_COMPATIBLE && trim($finalTemplateText) != '') {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'b1.png');
						} elseif ($attr_label == ProductCompHelper::COMP_TYPE_INCOMPATIBLE) {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'r1.png');
						} elseif ($attr_label == ProductCompHelper::COMP_TYPE_OC1) {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'oc1.png');
						} elseif ($attr_label == ProductCompHelper::COMP_TYPE_OC2) {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'oc2.png');
						} elseif ($attr_label == ProductCompHelper::COMP_TYPE_OC3) {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'oc3.png');
						}
					} else {
						if ($attr_label == ProductCompHelper::COMP_TYPE_COMPATIBLE) {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'g1.png');
						} elseif ($attr_label == ProductCompHelper::COMP_TYPE_PI) {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'b1.png');
						} elseif ($attr_label == ProductCompHelper::COMP_TYPE_INCOMPATIBLE) {
							$compatibility = $this->prodCompHelper->getImage('default/images/', 'r1.png');
						}
					}
					$compatibility_array[$count]['compatiibility'] = $compatibility;

					$count++;
				}
			}
		}
		if ($compType == "Case") {
			return ['data' => $compatibility_array, 'max_cooler_height' => $maxCoolerSize];
		} else {
			return $compatibility_array;
		}
	}

	/**
	 * Return Template Text Content With Join
	 * @param  [type] $tt1 [description]
	 * @param  [type] $tt2 [description]
	 * @param  [type] $tt3 [description]
	 * @param  [type] $tt4 [description]
	 * @return string
	 */
	public function getTemplateText($tt1, $tt2, $tt3, $tt4) {
		$name = '';
		$storeId = $this->storeManager->getStore()->getId();
		if ($tt1 != '-1') {
			$templateText1 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $tt1])->getFirstItem();
			$name .= $templateText1->getTemplateTextComment();
		}
		if ($tt2 != '-1') {
			$templateText2 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $tt2])->getFirstItem();
			$name .= $templateText2->getTemplateTextComment();
		}
		if ($tt3 != '-1') {
			$templateText3 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $tt3])->getFirstItem();
			$name .= $templateText3->getTemplateTextComment();
		}
		if ($tt4 != '-1') {
			$templateText4 = $this->templatetextFactory->create()->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $tt4])->getFirstItem();
			$name .= $templateText4->getTemplateTextComment();
		}
		return $name;
	}
}
