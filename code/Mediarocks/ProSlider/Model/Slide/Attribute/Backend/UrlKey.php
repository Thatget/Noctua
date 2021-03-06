<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [19-3-2019]
 */
namespace Mediarocks\ProSlider\Model\Slide\Attribute\Backend;

use \Magento\Catalog\Model\Category;
use \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

/**
 * Class UrlKey
 */
class UrlKey extends AbstractBackend {
	/**
	 * @var \SIT\GeneralNews\Model\ResourceModel\News
	 */
	protected $_slide;

	/**
	 * @var \Magento\Catalog\Model\Category
	 */
	protected $_category;

	/**
	 * @var int $minimumValueLength
	 */
	protected $minimumValueLength = 0;

	/**
	 * [__construct description]
	 * @param \SIT\GeneralNews\Model\ResourceModel\News $generalNews [description]
	 * @param \Magento\Catalog\Model\Category           $category    [description]
	 */
	public function __construct(
		\Mediarocks\ProSlider\Model\ResourceModel\Slide $slide,
		Category $category
	) {
		$this->_slide = $slide;
		$this->_category = $category;
	}
	/**
	 * @param \Magento\Framework\DataObject $object
	 *
	 * @return $this
	 */
	public function beforeSave($object) {
		$attributeName = $this->getAttribute()->getName();
		$urlKey = $object->getData($attributeName);
		if ($urlKey == '') {
			$urlKey = $object->getNewsTitle();
		}
		$urlKey = $this->formatUrlKey($urlKey);
		$validKey = false;
		while (!$validKey) {
			$entityId = $this->_slide->checkUrlKey($urlKey, $object->getStoreId(), false);
			if ($entityId == $object->getId() || empty($entityId)) {
				$validKey = true;
			} else {
				$parts = explode('-', $urlKey);
				$last = $parts[count($parts) - 1];
				if (!is_numeric($last)) {
					$urlKey = $urlKey . '-1';
				} else {
					$suffix = '-' . ($last + 1);
					unset($parts[count($parts) - 1]);
					$urlKey = implode('-', $parts) . $suffix;
				}
			}
		}
		$object->setData($attributeName, $urlKey);
		$this->validateLength($object);

		return parent::beforeSave($object);
	}

	/**
	 * Format url key as per category vise
	 * @return string
	 */
	public function formatUrlKey($str) {
		$urlKey = preg_replace('#[^0-9a-z]+#i', '-', $this->_category->formatUrlKey($str));
		$urlKey = strtolower($urlKey);
		$urlKey = trim($urlKey, '-');
		return $urlKey;
	}

	/**
	 * Validate length
	 *
	 * @param \Magento\Framework\DataObject $object
	 *
	 * @return bool
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function validateLength($object) {
		/** @var string $attributeCode */
		$attributeCode = $this->getAttribute()->getAttributeCode();
		/** @var int $value */
		$value = (int) $object->getData($attributeCode);
		/** @var int $minimumValueLength */
		$minimumValueLength = $this->getMinimumValueLength();

		if ($this->getAttribute()->getIsRequired() && $value <= $minimumValueLength) {
			throw new \Magento\Framework\Exception\LocalizedException(
				__('The value of attribute "%1" must be greater than %2', $attributeCode, $minimumValueLength)
			);
		}

		return true;
	}

	/**
	 * Get minimum attribute value length
	 *
	 * @return int
	 */
	public function getMinimumValueLength() {
		return $this->minimumValueLength;
	}
}
