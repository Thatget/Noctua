<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Block\Adminhtml;

class AssignProducts extends \Magento\Backend\Block\Template {
	/**
	 * Block template
	 *
	 * @var string
	 */
	protected $_template = 'products/assign_products.phtml';

	/**
	 * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
	 */
	protected $blockGrid;

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;

	/**
	 * @var \Magento\Framework\Json\EncoderInterface
	 */
	protected $jsonEncoder;

	/**
	 * @var \SIT\ProductReviewNew\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\Block\Template\Context    $context        [description]
	 * @param \Magento\Framework\Registry                $registry       [description]
	 * @param \Magento\Framework\Json\EncoderInterface   $jsonEncoder    [description]
	 * @param \SIT\ProductReviewNew\Model\ProductFactory $productFactory [description]
	 * @param array                                      $data           [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\SIT\ProductReviewNew\Model\ProductFactory $productFactory,
		array $data = []
	) {
		$this->registry = $registry;
		$this->jsonEncoder = $jsonEncoder;
		$this->productFactory = $productFactory;
		parent::__construct($context, $data);
	}

	/**
	 * Retrieve instance of grid block
	 *
	 * @return \Magento\Framework\View\Element\BlockInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getBlockGrid() {
		if (null === $this->blockGrid) {
			$this->blockGrid = $this->getLayout()->createBlock(
				'SIT\ProductReviewNew\Block\Adminhtml\Tab\Productgrid',
				'category.product.grid'
			);
		}
		return $this->blockGrid;
	}

	/**
	 * Return HTML of grid block
	 *
	 * @return string
	 */
	public function getGridHtml() {
		return $this->getBlockGrid()->toHtml();
	}

	/**
	 * @return string
	 */
	public function getProductsJson() {
		$entity_id = $this->getRequest()->getParam('entity_id');
		$productFactory = $this->productFactory->create()->getCollection();
		$productFactory->addFieldToSelect(['product_id', 'position']);
		$productFactory->addFieldToFilter('productreview_id', ['eq' => $entity_id]);
		$result = [];
		if (!empty($productFactory->getData())) {
			foreach ($productFactory->getData() as $faqProducts) {
				$result[$faqProducts['product_id']] = $faqProducts['position'];
			}
			return $this->jsonEncoder->encode($result);
		}
		return '{}';
	}

	public function getItem() {
		return $this->registry->registry('my_item');
	}
}
