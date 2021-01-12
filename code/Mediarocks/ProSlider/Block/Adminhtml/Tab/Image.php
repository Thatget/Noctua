<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [03-04-2019]
 */
namespace Mediarocks\ProSlider\Block\Adminhtml\Tab;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use SIT\MainAdmin\Helper\Data;

class Image extends AbstractRenderer {

	/**
	 * @var StoreManagerInterface
	 */
	private $_storeManager;

	/**
	 * @param \Magento\Backend\Block\Context $context
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		StoreManagerInterface $storemanager,
		Data $helperData,
		array $data = []
	) {
		$this->_storeManager = $storemanager;
		parent::__construct($context, $data);
		$this->_authorization = $context->getAuthorization();
	}
	/**
	 * Renders grid column
	 *
	 * @param Object $row
	 * @return  image with src
	 */
	public function render(DataObject $row) {
		$media_path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return '<img src="' . $media_path . $this->_getValue($row) . '" width="50"/>';
	}
}