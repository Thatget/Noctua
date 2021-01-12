<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [01-04-2019]
 */
namespace BL\FileAttributes\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use SIT\MainAdmin\Helper\Data as MainAdminHelper;

class File extends AbstractModifier {
	/**
	 * @var ArrayManager
	 */
	protected $arrayManager;

	/**
	 * @var StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * [__construct description]
	 * @param ArrayManager          $arrayManager [description]
	 * @param StoreManagerInterface $storeManager [description]
	 */
	public function __construct(
		ArrayManager $arrayManager,
		StoreManagerInterface $storeManager
	) {
		$this->arrayManager = $arrayManager;
		$this->_storeManager = $storeManager;
	}

	public function modifyMeta(array $meta) {
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl();
		$fieldCode = [MainAdminHelper::MANUAL_VARIABLE, MainAdminHelper::INFOSHEET_VARIABLE];
		$fieldHtml = '';
		foreach ($fieldCode as $key => $valueCode) {
			$elementPath = $this->arrayManager->findPath($valueCode, $meta, null, 'children');
			$containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $valueCode, $meta, null, 'children');

			if (!$elementPath) {
				return $meta;
			}

			if ($valueCode == MainAdminHelper::INFOSHEET_VARIABLE) {
				$fieldHtml = 'BL_FileAttributes/elements/fileinfosheet';
			} else if ($valueCode == MainAdminHelper::MANUAL_VARIABLE) {
				$fieldHtml = 'BL_FileAttributes/elements/filemanual';
			}

			$meta = $this->arrayManager->merge(
				$containerPath,
				$meta,
				[
					'children' => [
						$valueCode => [
							'arguments' => [
								'data' => [
									'config' => [
										'elementTmpl' => $fieldHtml,
										'tooltipdata' => [
											'description' => $baseUrl,
										],
									],
								],
							],
						],
					],
				]
			);
		}
		return $meta;
	}

	/**
	 * {@inheritdoc}
	 */
	public function modifyData(array $data) {
		return $data;
	}
}
