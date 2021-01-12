<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
use Magento\Framework\App\ResourceConnection;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\DataObject $dataObject,
        \Magento\Framework\App\ResponseInterface $response,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        $this->actionFactory = $actionFactory;
        $this->dataObject = $dataObject;
        $this->_response = $response;
        $this->productFaqFactory = $productFaqFactory;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
    }

    /**
     * Validate and  modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        $urlParams = explode("/", $identifier);

        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from(
                ['cv' => 'sit_productcompatibility_productcompatibility_varchar'],
                ['value']
            )->where('attribute_id = ?', 977);
        $compUrls = $connection->fetchAll($select);

        $compUrlData = [];
        foreach ($compUrls as $key => $value) {
            $compUrlData[] = $value['value'];
        }
        /**
         * $urlParams[0] = route name
         * $urlParams[1] = url key
         * If both set than call custom route
         */
        if (in_array("mainboard", $urlParams) && count($urlParams) == 2) {
            $request->setModuleName('sit_productcompatibility')->setControllerName('mainboard')->setActionName('view')->setParam('url_key', $urlParams[1])->setParam('comp_type', 'Mainboard');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            if (in_array($urlParams[1], $compUrlData)) {
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward', ['request' => $request]
                );
            }
        } else if (in_array("cpu", $urlParams) && count($urlParams) == 2) {
            $request->setModuleName('sit_productcompatibility')->setControllerName('cpu')->setActionName('view')->setParam('url_key', $urlParams[1])->setParam('comp_type', 'CPU');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            if (in_array($urlParams[1], $compUrlData)) {
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward', ['request' => $request]
                );
            }
        } else if (in_array("case", $urlParams) && count($urlParams) == 2) {
            $request->setModuleName('sit_productcompatibility')->setControllerName('caseview')->setActionName('view')->setParam('url_key', $urlParams[1])->setParam('comp_type', 'Case');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            if (in_array($urlParams[1], $compUrlData)) {
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward', ['request' => $request]
                );
            }
        } else if (in_array("ram", $urlParams) && count($urlParams) == 2) {
            $request->setModuleName('sit_productcompatibility')->setControllerName('ram')->setActionName('view')->setParam('url_key', $urlParams[1])->setParam('comp_type', 'RAM');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            if (in_array($urlParams[1], $compUrlData)) {
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward', ['request' => $request]
                );
            }
        } else {
            return;
        }
        return null;
    }
}
