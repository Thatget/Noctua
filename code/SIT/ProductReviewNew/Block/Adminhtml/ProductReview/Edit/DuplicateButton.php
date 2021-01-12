<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Block\Adminhtml\ProductReview\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DuplicateButton
 */
class DuplicateButton implements ButtonProviderInterface {
	/**
	 * Url Builder
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $urlBuilder;

	/**
	 * Registry
	 *
	 * @var Registry
	 */
	protected $registry;

	/**
	 * @param Context  $context  [description]
	 * @param Registry $registry [description]
	 */
	public function __construct(
		Context $context,
		Registry $registry
	) {
		$this->urlBuilder = $context->getUrlBuilder();
		$this->registry = $registry;
	}

	/**
	 * @return array
	 */
	public function getButtonData() {
		$entity_id = $this->registry->registry('entity_id');
		if ($entity_id > 0) {
			return [
				'label' => __('Duplicate'),
				'on_click' => sprintf("location.href = '%s';", $this->getDuplicateUrl('*/*/duplicate', ['entity_id' => $entity_id, 'edit_duplicate' => 1])),
				'sort_order' => 40,
			];
		}
	}

	/**
	 * @return string
	 */
	public function getDuplicateUrl($route = '', $params = []) {
		return $this->urlBuilder->getUrl($route, $params);
	}
}
