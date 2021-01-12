<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SIT\MainAdmin\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\UrlInterface;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * UrlRewrite Controller Router
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Router extends \Magento\UrlRewrite\Controller\Router {
	/**
	 * @var \Magento\Framework\App\ActionFactory
	 */
	protected $actionFactory;

	/**
	 * @var UrlInterface
	 */
	protected $url;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var HttpResponse
	 */
	protected $response;

	/**
	 * @var \Magento\UrlRewrite\Model\UrlFinderInterface
	 */
	protected $urlFinder;

	/**
	 * @param \Magento\Framework\App\ActionFactory $actionFactory
	 * @param UrlInterface $url
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\App\ResponseInterface $response
	 * @param UrlFinderInterface $urlFinder
	 */
	public function __construct(
		\Magento\Framework\App\ActionFactory $actionFactory,
		UrlInterface $url,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ResponseInterface $response,
		UrlFinderInterface $urlFinder
	) {
		$this->actionFactory = $actionFactory;
		$this->url = $url;
		$this->storeManager = $storeManager;
		$this->response = $response;
		$this->urlFinder = $urlFinder;
	}

	/**
	 * Match corresponding URL Rewrite and modify request.
	 *
	 * @param RequestInterface|HttpRequest $request
	 *
	 * @return ActionInterface|null
	 */
	public function match(RequestInterface $request) {
		/**
		 * Changes : URL Rewrite
		 */
		$url = rtrim($request->getPathInfo(), "/");
		$urlRewrite = $this->urlFinder->findOneByData([
			UrlRewrite::REQUEST_PATH => ltrim($url, '/'),
			UrlRewrite::STORE_ID => $this->storeManager->getStore()->getId(),
		]);
		$lastRequestPath = null;
		if (!isset($urlRewrite)) {
			$requestPath = $url;
			$lastRequestPath = explode('/', $requestPath);
			$lastRequestPath = end($lastRequestPath);

		}
		$url = rtrim($request->getPathInfo(), "/");
		$rewrite = $this->getRewrite(
			$url,
			$this->storeManager->getStore()->getId(),
			$lastRequestPath
		);
		/**
		 * Changes : URL Rewrite
		 */
		if ($rewrite === null) {
			//No rewrite rule matching current URl found, continuing with
			//processing of this URL.
			return null;
		}
		if ($rewrite->getRedirectType()) {
			//Rule requires the request to be redirected to another URL
			//and cannot be processed further.
			return $this->processRedirect($request, $rewrite);
		}
		//Rule provides actual URL that can be processed by a controller.
		$request->setAlias(
			UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
			$rewrite->getRequestPath()
		);
		$request->setPathInfo('/' . $rewrite->getTargetPath());
		return $this->actionFactory->create(
			\Magento\Framework\App\Action\Forward::class
		);
	}

	/**
	 * @param RequestInterface $request
	 * @param UrlRewrite $rewrite
	 *
	 * @return ActionInterface|null
	 */
	protected function processRedirect($request, $rewrite) {
		$target = $rewrite->getTargetPath();
		if ($rewrite->getEntityType() !== Rewrite::ENTITY_TYPE_CUSTOM
			|| ($prefix = substr($target, 0, 6)) !== 'http:/' && $prefix !== 'https:'
		) {
			$target = $this->url->getUrl('', ['_direct' => $target]);
		}
		return $this->redirect($request, $target, $rewrite->getRedirectType());
	}

	/**
	 * @param RequestInterface|HttpRequest $request
	 * @param string $url
	 * @param int $code
	 * @return ActionInterface
	 */
	protected function redirect($request, $url, $code) {
		$this->response->setRedirect($url, $code);
		$request->setDispatched(true);

		return $this->actionFactory->create(Redirect::class);
	}

	/**
	 * @param string $requestPath
	 * @param int $storeId
	 * @return UrlRewrite|null
	 */
	protected function getRewrite($requestPath, $storeId, $lastRequestPath = null) {
		if (strstr($requestPath, ".html")) {
			$requestPath = str_replace(".html", "", $requestPath);
		}
		if ($lastRequestPath !== null) {
			$lastRequestPath = '/' . $lastRequestPath;

			if (substr_count($requestPath, "products/") > 0) {

				$flag = strstr($requestPath, ".html");
				if (!$flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
				} else if ($flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
					$requestPath = str_replace(".html", "", $requestPath);
				}

			} elseif (substr_count($requestPath, "support/compatibility-lists") > 0) {
				$flag = strstr($requestPath, ".html");
				if (!$flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
				} else if ($flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
					$requestPath = str_replace(".html", "", $requestPath);
				}
			} elseif (substr_count($requestPath, "home-product/") > 0) {
				$flag = strstr($requestPath, ".html");
				if (!$flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
				} else if ($flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
					$requestPath = str_replace(".html", "", $requestPath);
				}
			} else {
				$flag = strstr($requestPath, ".html");
				if (!$flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
				} else if ($flag) {
					$requestPath = $this->str_lreplace($lastRequestPath, '', $requestPath);
					$requestPath = str_replace(".html", "", $requestPath);
				}
			}
		}
		return $this->urlFinder->findOneByData([
			UrlRewrite::REQUEST_PATH => ltrim($requestPath, '/'),
			UrlRewrite::STORE_ID => $storeId,
		]);
	}
	public function str_lreplace($search, $replace, $subject) {

		$pos = strrpos($subject, $search);
		if ($pos !== false) {
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}
		return $subject;
	}
}
