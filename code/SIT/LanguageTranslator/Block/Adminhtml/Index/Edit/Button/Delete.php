<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\LanguageTranslator\Block\Adminhtml\Index\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface {
	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @param Context $context
	 * @param AuthorRepositoryInterface $authorRepository
	 */

	public function __construct(
		Context $context
	) {
		$this->context = $context;
	}

	/**
	 * get button data
	 *
	 * @return array
	 */
	public function getButtonData() {
		$data = [];
		$translation_id = $this->context->getRequest()->getParam('translation_id');
		if ($translation_id) {
			$data = [
				'label' => __('Delete'),
				'class' => 'delete',
				'on_click' => 'deleteConfirm(\'' . __(
					'Are you sure you want to do this?'
				) . '\', \'' . $this->getDeleteUrl() . '\')',
				'sort_order' => 20,
			];
		}
		return $data;
	}

	/**
	 * @return string
	 */
	public function getDeleteUrl() {
		$translation_id = $this->context->getRequest()->getParam('translation_id');
		return $this->getUrl('*/*/delete', ['translation_id' => $translation_id]);
	}
}
