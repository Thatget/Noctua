<?php

namespace SIT\ProductFaqNew\Model\Source;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Options tree for "Categories" field
 */
class GetParentCategory implements OptionSourceInterface {

	/**
	 * @var \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory
	 */
	protected $categoryCollectionFactory;

	/**
	 * @var RequestInterface
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $categoriesTree;

	/**
	 * @param \SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory [description]
	 * @param RequestInterface                                                  $request                   [description]
	 */
	public function __construct(
		\SIT\ProductFaqNew\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
		RequestInterface $request
	) {
		$this->categoryCollectionFactory = $categoryCollectionFactory;
		$this->request = $request;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toOptionArray() {
		$cat_id = $this->request->getParam('id');
		if ($cat_id != "") {
			$matchingNamesCollection = $this->categoryCollectionFactory->create();
			$matchingNamesCollection->addFieldToFilter('status', ['eq' => 1])->addFieldToFilter('parent_cat_name', ['neq' => $cat_id]);
		} else {
			$matchingNamesCollection = $this->categoryCollectionFactory->create();
			$matchingNamesCollection->addFieldToFilter('status', ['eq' => 1]);
		}
		if ($this->categoriesTree === null) {
			$this->categoriesTree[0] = [
				'value' => 0,
				'label' => 'Default Category',
			];
			foreach ($matchingNamesCollection as $key => $value) {
				$this->categoriesTree[$key + 1]['value'] = $value['id'];
				$this->categoriesTree[$key + 1]['label'] = $value['cat_name'];
			}

		}
		return $this->categoriesTree;
	}
}