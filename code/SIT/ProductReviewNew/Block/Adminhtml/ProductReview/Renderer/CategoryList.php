<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Block\Adminhtml\ProductReview\Renderer;

class CategoryList extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $_productFactory;

	/**
	 * @var \Magento\Catalog\Model\CategoryFactory
	 */
	protected $_categoryFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\Block\Context             $context         [description]
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager    [description]
	 * @param \Magento\Catalog\Model\ProductFactory      $productFactory  [description]
	 * @param \Magento\Catalog\Model\CategoryFactory     $categoryFactory [description]
	 * @param array                                      $data            [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		array $data = []) {
		parent::__construct($context, $data);
		$this->_storeManager = $storeManager;
		$this->_productFactory = $productFactory;
		$this->_categoryFactory = $categoryFactory;
	}

	public function render(\Magento\Framework\DataObject $row) {
		$product = $this->_productFactory->create()->load($row->getEntityId());
		$cats = $product->getCategoryIds();
		$allCats = '';
		foreach ($cats as $key => $cat) {
			$_category = $this->_categoryFactory->create()->load($cat);
			$allCats .= $_category->getName();
			if ($key < count($cats) - 1) {
				$allCats .= ' ,';
			}

		}
		return $allCats;
	}

}