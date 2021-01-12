<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh
 */
namespace SIT\ProductCompatibility\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action {

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resource;

	/**
	 * @var \SIT\ProductCompatibility\Model\ProductDuplicateFactory
	 */
	protected $duplicateFactory;

	/**
	 * @var \SIT\ProductCompatibility\Model\ProductCompatibilityFactory
	 */
	protected $compatibilityFactory;

	/**
	 * @var \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory
	 */
	protected $collFactory;

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $product;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var Magento\Framework\Filesystem\DirectoryList
	 */
	protected $_dir;

	/**
	 * @param \Magento\Framework\App\Action\Context                                                $context              [description]
	 * @param \Magento\Framework\View\Result\PageFactory                                           $resultPageFactory    [description]
	 * @param \Magento\Framework\App\ResourceConnection                                            $resource             [description]
	 * @param \SIT\ProductCompatibility\Model\ProductDuplicateFactory                              $duplicateFactory     [description]
	 * @param \SIT\ProductCompatibility\Model\ProductCompatibilityFactory                          $compatibilityFactory [description]
	 * @param \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $collFactory          [description]
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory                       $product              [description]
	 * @param \Magento\Framework\Filesystem\DirectoryList                                          $dir                  [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                                           $storeManager         [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\ResourceConnection $resource,
		\SIT\ProductCompatibility\Model\ProductDuplicateFactory $duplicateFactory,
		\SIT\ProductCompatibility\Model\ProductCompatibilityFactory $compatibilityFactory,
		\SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $collFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $product,
		\Magento\Framework\Filesystem\DirectoryList $dir,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->resource = $resource;
		$this->duplicateFactory = $duplicateFactory;
		$this->compatibilityFactory = $compatibilityFactory;
		$this->collFactory = $collFactory;
		$this->product = $product;
		$this->_dir = $dir;
		$this->_storeManager = $storeManager;
		parent::__construct($context);
	}

	public function execute() {

		/**
		 * Run script for set template ids in table [START]
		 */
		// $read = $this->resource->getConnection('core_read');
		// $write = $this->resource->getConnection('core_write');
		// $default_table = $this->resource->getTableName('default_text_product_compatibility');
		// //$rs_default = $read->fetchAll("SELECT * FROM " . $default_table . " WHERE extra_comment IS NOT NULL AND comp_id > 17722 ORDER BY comp_id");
		// $rs_default = $read->fetchAll("SELECT * FROM " . $default_table . " WHERE comp_id > 17947 ORDER BY comp_id");
		// echo "<pre>";
		// $comp_id_arr = [];
		// $count = 0;
		// foreach ($rs_default as $key => $value) {
		// 	$data = $this->duplicateFactory->create()->load($value['comp_id'], 'old_id');
		// 	// if ((trim($data['id']) != "") && (trim($value['extra_comment']) != "")) {
		// 	$comp_id_arr[$count]['comp_id'] = $data['id'];
		// 	$comp_id_arr[$count]['store_id'] = $value['store_id'];
		// 	$comp_id_arr[$count]['template_text_1'] = $value['template_id'];
		// 	$comp_id_arr[$count]['template_text_2'] = $value['template_id_2'];
		// 	$comp_id_arr[$count]['template_text_3'] = $value['template_id_3'];
		// 	$comp_id_arr[$count]['template_text_4'] = $value['template_id_4'];
		// 	if ((trim($data['id']) != "") && (trim($value['extra_comment']) != "")) {
		// 		if (strpos($value['extra_comment'], '<li>') !== false) {
		// 			$comp_id_arr[$count]['extra_comment'] = trim($value['extra_comment']);
		// 		} else {
		// 			$comp_id_arr[$count]['extra_comment'] = "<li>" . trim($value['extra_comment']) . "</li>";
		// 		}
		// 	}
		// 	if (trim($value['max_cooler_height']) != null) {
		// 		$comp_id_arr[$count]['max_cooler_height'] = $value['max_cooler_height'];
		// 	}
		// 	/*if (trim($value['extra_comment']) != strip_tags(trim($value['extra_comment']))) {
		// 			                $comp_id_arr[$count]['extra_comment'] = $value['extra_comment'];
		// 			                } else {
		// 			                $comp_id_arr[$count]['extra_comment'] = "<li>" . $value['extra_comment'] . "</li>";
		// 		*/
		// 	//$comp_id_arr[$count]['extra_comment'] = $value['extra_comment'];
		// 	$count++;
		// 	// }
		// }
		// print_r($comp_id_arr);
		// echo count($comp_id_arr);
		// exit;

		// foreach ($comp_id_arr as $key => $value) {
		// 	$c[] = $value['comp_id'];
		// 	$comp = $this->compatibilityFactory->create()->load($value['comp_id']);
		// 	$comp->setData('template_text_1', $value['template_text_1']);
		// 	$comp->setData('template_text_2', $value['template_text_2']);
		// 	$comp->setData('template_text_3', $value['template_text_3']);
		// 	$comp->setData('template_text_4', $value['template_text_4']);
		// 	$comp->setStoreId($value['store_id']);
		// 	if (isset($value['extra_comment'])) {
		// 		if (trim($value['extra_comment']) != strip_tags(trim($value['extra_comment']))) {
		// 			$comp->setCompExtraComment($value['extra_comment']);
		// 		} else {
		// 			$comp->setCompExtraComment("<li>" . $value['extra_comment'] . "</li>");
		// 		}
		// 	}
		// 	$comp->setData('max_cooler_height', $value['max_cooler_height']);
		// 	//print_r($comp->getData());
		// 	$comp->save();
		// }

		/**
		 * Run script for set template ids in table [END]
		 */

	}
}
