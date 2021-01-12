<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\FixUrls\Plugin\UrlRewrite\Model\StoreSwitcher;

use Magento\Store\Api\Data\StoreInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class RewriteUrl
{
    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RequestFactory
     */
    private $requestFactory;

    /**
     * @param UrlFinderInterface $urlFinder
     * @param \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory
     */
    public function __construct(
        UrlFinderInterface $urlFinder,
        \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory
    ) {
        $this->urlFinder = $urlFinder;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Edit cms url path
     *
     * @param \Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl $subject
     * @param callable $proceed
     * @param StoreInterface $fromStore
     * @param StoreInterface $targetStore
     * @param string $redirectUrl
     */
    public function aroundSwitch(
        \Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl $subject,
        callable $proceed,
        $fromStore,
        $targetStore,
        $redirectUrl
    ) {
        $targetUrl = $redirectUrl;
        /** @var \Magento\Framework\HTTP\PhpEnvironment\Request $request */
        $request = $this->requestFactory->create(['uri' => $targetUrl]);

        $urlPath = ltrim($request->getPathInfo(), '/');

        if ($targetStore->isUseStoreInUrl()) {
            // Remove store code in redirect url for correct rewrite search
            $storeCode = preg_quote($targetStore->getCode() . '/', '/');
            $pattern = "@^($storeCode)@";
            $urlPath = preg_replace($pattern, '', $urlPath);
        }

        $oldStoreId = $fromStore->getId();
        $oldRewrite = $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => $urlPath,
            UrlRewrite::STORE_ID => $oldStoreId,
        ]);
        if ($oldRewrite) {
            $targetUrl = $targetStore->getBaseUrl();
            // look for url rewrite match on the target store
            $currentRewrite = $this->urlFinder->findOneByData([
                UrlRewrite::TARGET_PATH => $oldRewrite->getTargetPath(),
                UrlRewrite::STORE_ID => $targetStore->getId(),
            ]);
            if ($currentRewrite) {
                $targetUrl .= $currentRewrite->getRequestPath();
            } else {
                $currentRewrite = $this->urlFinder->findOneByData([
                    UrlRewrite::REQUEST_PATH => $urlPath,
                    UrlRewrite::STORE_ID => $targetStore->getId(),
                ]);
                if ($currentRewrite) {
                    $targetUrl .= $currentRewrite->getRequestPath();
                }
            }
        } else {
            $existingRewrite = $this->urlFinder->findOneByData([
                UrlRewrite::REQUEST_PATH => $urlPath
            ]);
            if ($existingRewrite) {
                /** @var \Magento\Framework\App\Response\Http $response */
                $targetUrl = $targetStore->getBaseUrl();
            }
        }
        return $targetUrl;
    }
}
