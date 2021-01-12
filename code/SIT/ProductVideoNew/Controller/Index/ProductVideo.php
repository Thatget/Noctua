<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use SIT\ProductVideoNew\Block\ProductVideo\ProductVideoView;

class ProductVideo extends Action {

	/**
	 * @var PageFactory
	 */
	protected $pageFactory;

	/**
	 * @var ProductVideoView
	 */
	protected $productVideoView;

	/**
	 * @var JsonFactory
	 */
	protected $jsonFactory;

	/**
	 * @param Context          $context          [description]
	 * @param PageFactory      $pageFactory      [description]
	 * @param JsonFactory      $jsonFactory      [description]
	 * @param ProductVideoView $productVideoView [description]
	 */
	public function __construct(
		Context $context,
		PageFactory $pageFactory,
		JsonFactory $jsonFactory,
		ProductVideoView $productVideoView
	) {
		$this->pageFactory = $pageFactory;
		$this->jsonFactory = $jsonFactory;
		$this->productVideoView = $productVideoView;
		parent::__construct($context);
	}

	/**
	 * @return JsonFactory
	 */
	public function execute() {
		$resultPage = $this->pageFactory->create();
		$result = $this->jsonFactory->create();
		$current_id = $this->getRequest()->getParam('id');
		$current_collection_null = $this->productVideoView->getProductVideoData($current_id, 'null');
		$current_collection_notNull = $this->productVideoView->getProductVideoData($current_id, 'notnull');

		$block = $resultPage->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId('video_reviews_pretext')->toHtml();
		return $result->setData(["nullCollection" => $current_collection_null, "block" => $block, "notNullCollection" => $current_collection_notNull]);
	}
}
