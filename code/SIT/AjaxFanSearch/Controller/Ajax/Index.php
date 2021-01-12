<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\AjaxFanSearch\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;

/**
 * For display products on filter selected product in fan category page.
 */

class Index extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @var \SIT\AjaxFanSearch\Block\AjaxFanSearch\ListFanData
	 */
	protected $ajaxFanBlock;

	/**
	 * @var \Magento\Catalog\Model\CategoryFactory
	 */
	protected $categoryFactory;

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $productCollectionFactory;

	const FAN_CATEGORY_KEY = 'fan';

	const REDUX_CATEGORY_KEY = 'line-redux';

	const INDUSTRIAL_CATEGORY_KEY = 'line-industrial';

	const CHROMAX_CATEGORY_KEY = 'line-chromax';

	const SIZE_GROUP_IMAGE = 'size_group_image';

	const SIZE_GROUP_HEADER_IMAGE = 'size_group_header_image';

	const SIZE_MM_ATTR_TEXT = 'size_mm';

	/**
	 * [__construct description]
	 * @param \Magento\Framework\App\Action\Context                          $context                  [description]
	 * @param \Magento\Framework\View\Result\PageFactory                     $resultPageFactory        [description]
	 * @param \Magento\Framework\Json\Helper\Data                            $jsonHelper               [description]
	 * @param \Magento\Catalog\Model\CategoryFactory                         $categoryFactory          [description]
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory [description]
	 * @param \SIT\AjaxFanSearch\Block\AjaxFanSearch\ListFanData             $ajaxFanBlock             [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\SIT\AjaxFanSearch\Block\AjaxFanSearch\ListFanData $ajaxFanBlock
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->jsonHelper = $jsonHelper;
		$this->categoryFactory = $categoryFactory;
		$this->productCollectionFactory = $productCollectionFactory;
		$this->ajaxFanBlock = $ajaxFanBlock;
		parent::__construct($context);
	}

	public function execute() {
		$resultPage = $this->resultPageFactory->create();
		$credentials = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
		$tab_name = $credentials['tab_name'];
		$fan_connector = $credentials['fan_connector'];
		$fan_size = $credentials['fan_size'];
		if (isset($credentials['fan_voltage'])) {
			$fan_voltage = $credentials['fan_voltage'];
		} else {
			$fan_voltage = '';
		}

		$fan_size_value = '';
		$fan_connector_value = '';
		$fan_voltage_value = '';
		if (!empty($fan_size)) {
			if (isset($fan_size['attr_value'])) {
				$fan_size_value = $fan_size['attr_value'];
			}
		}
		if (!empty($fan_connector)) {
			if (isset($fan_connector['attr_value'])) {
				$fan_connector_value = $fan_connector['attr_value'];
			}
		}
		if (!empty($fan_voltage)) {
			if (isset($fan_voltage['attr_value'])) {
				$fan_voltage_value = $fan_voltage['attr_value'];
			}
		}
		$blockData = $resultPage->getLayout()->createBlock('\SIT\AjaxFanSearch\Block\AjaxFanSearch\ListFanData')
			->setData("fan_size_value", $fan_size_value)
			->setData("connector_value", $fan_connector_value)
			->setData("voltage_value", $fan_voltage_value)
			->getFanSearchProList($tab_name);
		$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
		$response->setHeader('Content-type', 'application/json');
		$response->setContents($blockData);
		return $response;
	}
}
