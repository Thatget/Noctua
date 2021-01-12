<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Copyproductmeta\Controller\Adminhtml\Copyproductmeta;

class UpdateMetaData extends \Magento\Backend\App\Action {

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\App\Action\Context               $context           [description]
	 * @param \Magento\Catalog\Model\ProductFactory             $productFactory    [description]
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect          [description]
	 * @param \Magento\Framework\View\Result\PageFactory        $resultPageFactory [description]
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->productFactory = $productFactory;
		$this->redirect = $redirect;
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

	/**
	 * [execute set Meta data]
	 * @return [type] [description]
	 */
	public function execute() {
		$productId = $this->getRequest()->getParam('id');
		$storeId = $this->getRequest()->getParam('store');
		$product = $this->productFactory->create()->setStoreId($storeId)->load($productId);

		$productName = $product->getName();
		/*Remove html tags from description for meta data description*/
		$shortDescription = strip_tags($product->getShortDescription());

		$product->setMetaTitle($productName);
		$product->setMetaDescription($shortDescription);
		$product->save();

		$this->messageManager->addSuccess(__('Your Meta data is copied from product data.'));
		$productUrl = $this->redirect->getRefererUrl();
		return $this->_redirect($productUrl);
	}
}
