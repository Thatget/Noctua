<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Controller\News;

use Magento\Framework\Controller\ResultFactory;

class Kogeneralnews extends \Magento\Framework\App\Action\Action {

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\App\Action\Context      $context           [description]
	 * @param \Magento\Framework\Json\Helper\Data        $jsonHelper        [description]
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->jsonHelper = $jsonHelper;
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

	/**
	 * Return array of general news
	 *
	 * @return array
	 */
	public function execute() {
		$resultPage = $this->resultPageFactory->create();
		$paramArray = [];
		$blockData = $resultPage->getLayout()->createBlock('\SIT\GeneralNews\Block\GeneralNews\GeneralNews')->getNewsPageCollection();
		$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
		$response->setHeader('Content-type', 'application/json');
		$response->setContents($this->jsonHelper->jsonEncode($blockData));
		return $response;
	}
}
