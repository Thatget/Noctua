<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SIT\ProductTechNew\Model\ProductTechnology\Attribute\Backend;

/**
 * Catalog category image attribute backend model
 *
 * @api
 * @since 100.0.2
 */
class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend {
	/**
	 * @var \Magento\MediaStorage\Model\File\UploaderFactory
	 *
	 * @deprecated 101.0.0
	 */
	protected $_uploaderFactory;

	/**
	 * @var \Magento\Framework\Filesystem
	 *
	 * @deprecated 101.0.0
	 */
	protected $_filesystem;

	/**
	 * @var \Magento\MediaStorage\Model\File\UploaderFactory
	 *
	 * @deprecated 101.0.0
	 */
	protected $_fileUploaderFactory;

	/**
	 * @var \Psr\Log\LoggerInterface
	 *
	 * @deprecated 101.0.0
	 */
	protected $_logger;

	/**
	 * @var \Magento\Catalog\Model\ImageUploader
	 */
	private $imageUploader;

	/**
	 * @var string
	 */
	private $additionalData = '_additional_data_';

	/**
	 * RequestInterface
	 * @var $request
	 */
	protected $request;

	/**
	 * [__construct description]
	 * @param \Psr\Log\LoggerInterface                         $logger              [description]
	 * @param \Magento\Framework\Filesystem                    $filesystem          [description]
	 * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory [description]
	 * @param \Magento\Framework\App\RequestInterface          $request             [description]
	 */
	public function __construct(
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\App\RequestInterface $request
	) {
		$this->_filesystem = $filesystem;
		$this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_logger = $logger;
		$this->request = $request;
	}

	/**
	 * Gets image name from $value array.
	 * Will return empty string in a case when $value is not an array
	 *
	 * @param array $value Attribute value
	 * @return string
	 */
	private function getUploadedImageName($value) {
		if (is_array($value) && isset($value[0]['name'])) {
			return $value[0]['name'];
		}

		return '';
	}

	/**
	 * Avoiding saving potential upload data to DB
	 * Will set empty image attribute value if image was not uploaded
	 *
	 * @param \Magento\Framework\DataObject $object
	 * @return $this
	 * @since 101.0.8
	 */
	public function beforeSave($object) {
		$attributeName = $this->getAttribute()->getName();
		$actionDuplicate = $this->request->getParam('duplicate');
		$massDuplicate = $this->request->getParam('selected');
		$editDuplicate = $this->request->getParam('edit_duplicate');
		/*Set image value null when remove image using Delete Icon[MD][START][18-03-2019]*/
		$productTechImg = $object->getTechnologyImage();

		if (is_array($productTechImg)) {
			$value = $object->getData($attributeName);
			if ($imageName = $this->getUploadedImageName($value)) {
				if (strpos($imageName, '/') !== false) {
					$imageName = $imageName;
				} else {
					$firstName = substr($imageName, 0, 1);
					$secondName = substr($imageName, 1, 1);
					$imageName = '/' . $firstName . '/' . $secondName . '/' . $imageName;
				}
				$object->setData($this->additionalData . $attributeName, $value);
				$object->setData($attributeName, $imageName);
			} elseif (!is_string($value)) {
				$object->setData($attributeName, null);
			}
		} else if ($actionDuplicate == 1) {
			$object->setData($attributeName, $productTechImg);
		} else if ($massDuplicate) {
			$object->setData($attributeName, $productTechImg);
		} else if ($editDuplicate == 1) {
			$object->setData($attributeName, $productTechImg);
		} else {
			$object->setData($attributeName, null);
		}
		/*Set image value null when remove image using Delete Icon[MD][END][18-03-2019]*/
		return parent::beforeSave($object);
	}

	/**
	 * @return \Magento\Catalog\Model\ImageUploader
	 *
	 * @deprecated 101.0.0
	 */
	private function getImageUploader() {
		if ($this->imageUploader === null) {
			$this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()
				->get(\Magento\Catalog\CategoryImageUpload::class);
		}

		return $this->imageUploader;
	}

	/**
	 * Check if temporary file is available for new image upload.
	 *
	 * @param array $value
	 * @return bool
	 */
	private function isTmpFileAvailable($value) {
		return is_array($value) && isset($value[0]['tmp_name']);
	}

	/**
	 * Save uploaded file and set its name to category
	 *
	 * @param \Magento\Framework\DataObject $object
	 * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
	 */
	public function afterSave($object) {
		$value = $object->getData($this->additionalData . $this->getAttribute()->getName());
		if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
			try {
				$this->getImageUploader()->moveFileFromTmp($imageName);
			} catch (\Exception $e) {
				$this->_logger->critical($e);
			}
		}
		return $this;
	}
}
