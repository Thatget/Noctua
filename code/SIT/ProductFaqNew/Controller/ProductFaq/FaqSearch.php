<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\ProductFaq;

class FaqSearch extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
	 */
	protected $productFaqFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @param \Magento\Framework\App\Action\Context                               $context           [description]
	 * @param \Magento\Framework\View\Result\PageFactory                          $resultPageFactory [description]
	 * @param \Magento\Framework\Controller\Result\JsonFactory                    $resultJsonFactory [description]
	 * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager      [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->productFaqFactory = $productFaqFactory;
		$this->storeManager = $storeManager;
		parent::__construct($context);
	}

	public function execute() {
		$resultPage = $this->resultPageFactory->create();
		$result = $this->resultJsonFactory->create();
		$block = $resultPage->getLayout()
			->createBlock('SIT\ProductFaqNew\Block\ProductFaq\FaqQuestions')
			->setTemplate('SIT_ProductFaqNew::faq_question_other.phtml')
			->toHtml();
		$result->setData(['output' => $block]);
		return $result;
	}
}
?>