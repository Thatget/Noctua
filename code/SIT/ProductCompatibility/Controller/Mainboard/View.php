<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Mainboard;

class View extends \Magento\Framework\App\Action\Action {

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

	/**
	 * @param \Magento\Framework\App\Action\Context                                                $context           [description]
	 * @param \Magento\Framework\View\Result\PageFactory                                           $resultPageFactory [description]
	 * @param \SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $collectionFactory [description]
	 * @param \Magento\Store\Model\StoreManagerInterface                                            $storeManager      [description]
     * @param \Magento\Framework\App\ResponseInterface                                              $response          [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\CollectionFactory $collectionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->collectionFactory = $collectionFactory;
		$this->storeManager = $storeManager;
        $this->_response = $response;
		parent::__construct($context);
	}

	/**
	 * Execute view action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$this->_view->loadLayout();
		$resultPage = $this->resultPageFactory->create();
		$url_key = $this->getRequest()->getParam('url_key');
		$title = explode("_", $url_key);
		$titlex = str_replace("_"," ",$url_key);
        $this->_response->setRedirect("https://ncc.noctua.at/motherboards/?q=".$titlex , 301)->sendResponse();
        $resultPage->getConfig()->getTitle()->set('Mainboard ' . $title[0] . " | " . $titlex ." compatibility");
		$resultPage->getConfig()->setPageLayout('1column');
		$this->_view->renderLayout();
	}
}
