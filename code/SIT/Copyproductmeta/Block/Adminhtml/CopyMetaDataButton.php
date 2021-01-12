<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Copyproductmeta\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

class CopyMetaDataButton extends Generic {

	/**
	 * [__construct description]
	 * @param \Magento\Framework\App\RequestInterface $request [description]
	 * @param Context                                 $context [description]
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		Context $context
	) {
		$this->request = $request;
		$this->urlBuilder = $context->getUrlBuilder();
	}

	/**
	 * [getButtonData Disaplay button to product edit page]
	 * @return [type] [description]
	 */
	public function getButtonData() {
		$id = $this->request->getParam('id');
		$params = $this->request->getParams();
		$storeId = "";
		if (array_key_exists("store", $params)) {
			$storeId = $params["store"];
		} else {
			$storeId = 0;
		}
		if ($id) {
			return [
				'label' => __('Copy Meta Data'),
				'on_click' => sprintf("location.href = '%s'", $this->getUrl('sit_copyproductmeta/copyproductmeta/updatemetadata', ['id' => $id, 'store' => $storeId])),
				'sort_order' => 9,
			];
		}
	}

	/**
	 * [getUrl generate url for controller]
	 * @param  string $route  [description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function getUrl($route = '', $params = []) {
		return $this->urlBuilder->getUrl($route, $params);
	}
}
