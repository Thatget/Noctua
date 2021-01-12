<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\GeneralNews\Controller;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Router implements \Magento\Framework\App\RouterInterface {
	/**
	 * @var \Magento\Framework\App\ActionFactory
	 */
	protected $actionFactory;

	/**
	 * Event manager
	 *
	 * @var \Magento\Framework\Event\ManagerInterface
	 */
	protected $_eventManager;

	/**
	 * Store manager
	 *
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * News factory
	 *
	 * @var \SIT\GeneralNews\Model\NewsFactory
	 */
	protected $_newsFactory;

	/**
	 * Config primary
	 *
	 * @var \Magento\Framework\App\State
	 */
	protected $_appState;

	/**
	 * Url
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_url;

	/**
	 * Response
	 *
	 * @var \Magento\Framework\App\ResponseInterface
	 */
	protected $_response;

	/**
	 * [__construct description]
	 * @param \Magento\Framework\App\ActionFactory       $actionFactory [description]
	 * @param \Magento\Framework\Event\ManagerInterface  $eventManager  [description]
	 * @param \Magento\Framework\UrlInterface            $url           [description]
	 * @param \SIT\GeneralNews\Model\NewsFactory         $newsFactory   [description]
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager  [description]
	 * @param \Magento\Framework\App\ResponseInterface   $response      [description]
	 */
	public function __construct(
		\Magento\Framework\App\ActionFactory $actionFactory,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Framework\UrlInterface $url,
		\SIT\GeneralNews\Model\NewsFactory $newsFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ResponseInterface $response
	) {
		$this->actionFactory = $actionFactory;
		$this->_eventManager = $eventManager;
		$this->_url = $url;
		$this->_newsFactory = $newsFactory;
		$this->_storeManager = $storeManager;
		$this->_response = $response;
	}

	/**
	 * Validate and Match SIT GeneralNews and modify request
	 *
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return \Magento\Framework\App\ActionInterface|null
	 */
	public function match(\Magento\Framework\App\RequestInterface $request) {
		$identifier = trim($request->getPathInfo(), '/');

		$condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
		$this->_eventManager->dispatch(
			'sit_generalnews_controller_router_match_before',
			['router' => $this, 'condition' => $condition]
		);
		$identifier = $condition->getIdentifier();

		if ($condition->getRedirectUrl()) {
			$this->_response->setRedirect($condition->getRedirectUrl());
			$request->setDispatched(true);
			return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
		}

		if (!$condition->getContinue()) {
			return null;
		}

		/** @var \SIT\GeneralNews\Model\News $news */
		$news = $this->_newsFactory->create();
		$newsId = $news->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
		$newsData = $news->load($newsId);
		if (!$newsId || $newsData->getStatus() == 0) {
			return null;
		}

		$request->setModuleName('generalnews')->setControllerName('news')->setActionName('view')->setParam('news_id', $newsId);
		$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

		return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
	}
}
