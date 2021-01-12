<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */

namespace Mediarocks\ProSlider\Model\Option;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class CmsPageList implements ArrayInterface {

	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @param CollectionFactory $collectionFactory [description]
	 */
	public function __construct(
		CollectionFactory $collectionFactory
	) {
		$this->collectionFactory = $collectionFactory;
	}

	/**
	 * ui-form select category
	 * @return array [cms page list]
	 */
	public function toOptionArray() {

		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);
		$collection->getSelect()->group('identifier');
		$cms_pages = [];

		foreach ($collection as $values) {
			$cms_pages[] = ['value' => $values->getIdentifier(), 'label' => $values->getTitle()];
		}
		return $cms_pages;
	}
}