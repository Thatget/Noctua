<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller;

use Magento\Framework\App\ResourceConnection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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
     * @var \SIT\ProductFaqNew\Model\ProductFaqFactory
     */
    protected $productFaqFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\ActionFactory                                $actionFactory     [description]
     * @param \Magento\Framework\DataObject                                       $dataObject        [description]
     * @param \Magento\Framework\App\ResponseInterface                            $response          [description]
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory [description]
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager      [description]
     * @param ResourceConnection                                                  $resource          [description]
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
        $storeId = $this->storeManager->getStore()->getId();
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        $identifier = trim($request->getPathInfo(), '/');
        $urlParams = explode("/", $identifier);

        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);

        $identifier = $condition->getIdentifier();
        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
        }

        if (!$condition->getContinue()) {
            return null;
        }

        if ($condition->getContinue() && !in_array("general-faqs", $urlParams)) {
            $productFaq = $this->productFaqFactory->create();

            $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
            //$result = $connection->fetchAll("SELECT entity_id FROM sit_productfaqnew_productfaq_varchar WHERE attribute_id = 423 AND value = '" . $identifier . "'");
            $data = $this->resource->getConnection()
                ->select()
                ->from($connection->getTableName('sit_productfaqnew_productfaq_varchar'),'entity_id')
                ->where('attribute_id = ?', 423)
                ->where('value = ?',$identifier);
            $result = $this->resource->getConnection()->fetchAll($data);
            if (count($result) > 0) {
                $productFaqItem = $productFaq->setStoreId($storeId)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $result[0]['entity_id']])->getFirstItem();
                $faqId = $productFaqItem->getEntityId();
                $update_identifier = $productFaqItem->getUrlKey();
                if (!$productFaqItem->getEntityId() || $productFaqItem->getStatus() == 0) {
                    return null;
                }
                /**
                 * This condition is prevent multiple redirection
                 */
                if ($update_identifier != $identifier) {
                    /**
                     * Change url key according to store view
                     */
                    $this->_sendRedirectHeaders($update_identifier, true);
                }
                $request->setRouteName('productfaqs')->setControllerName('productfaq')->setActionName('view')->setParam('id', $faqId);
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $update_identifier);
                return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
            }
        } else if (in_array("general-faqs", $urlParams)) {
            $request->setRouteName('productfaqs')->setControllerName('productfaq')->setActionName('categoryfaq')->setParam('faqtitle', 'call');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
        } else {
            return;
        }
        return null;
    }

    protected function _sendRedirectHeaders($url, $isPermanent = false)
    {
        if ($isPermanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Location: ' . $url);
        exit;
    }
}
