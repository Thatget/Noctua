<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\AjaxSearch\Controller\Index;

/**
 * For display products on filter selected product in category page.
 */
class Catprodata extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\MainAdmin\Helper\Data
     */
    protected $sitHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context                          $context                  [description]
     * @param \Magento\Framework\View\Result\PageFactory                     $resultPageFactory        [description]
     * @param \Magento\Framework\Json\Helper\Data                            $jsonHelper               [description]
     * @param \Magento\Framework\Controller\Result\JsonFactory               $jsonFactory              [description]
     * @param \Magento\Catalog\Model\ProductFactory                          $productFactory           [description]
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager             [description]
     * @param \SIT\MainAdmin\Helper\Data                                     $sitHelper                [description]
     * @param \Magento\Catalog\Helper\Image                                  $imageHelper              [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\MainAdmin\Helper\Data $sitHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Registry $registry,
        \Swissup\Easytabs\Helper\Data $easyTab
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->jsonFactory = $jsonFactory;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->sitHelper = $sitHelper;
        $this->imageHelper = $imageHelper;
        $this->registry = $registry;
        $this->easyTab = $easyTab;
        parent::__construct($context);
    }
    /**
     * Product data based on requested product filter.
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultJson = $this->jsonFactory->create();
        $productArray = [];
        $credentials = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
        if ($credentials['productID'] && $credentials['productID'] != -1) {
            /**
             * For catalog search page [Start]
             */
            if (is_array($credentials['productID'])) {
                foreach ($credentials['productID'] as $key => $value) {
                    $productData = $this->productFactory->create()->load($value);
                    $productArray[$key]['id'] = $value;
                    $productArray[$key]['name'] = $productData->getName();
                    if (array_key_exists('cid', $credentials)) {
                        if ($credentials['cid']) {
                            $productArray[$key]['position'] = $this->getPosition($value);
                        }
                    }
                    $productArray[$key]['cmscontent'] = $this->sitHelper->getCmsFilterContent($productData->getData('feature_1_en'));
                    $productArray[$key]['left_image'] = $this->imageHelper->init($productData, 'small_image')->setImageFile($productData->getSmallImage())->constrainOnly(true)->keepAspectRatio(true)->keepTransparency(true)->keepFrame(false)->resize(100)->getUrl();
                    $productArray[$key]['right_image'] = $this->imageHelper->init($productData, 'thumbnail')->setImageFile($productData->getThumbnail())->getUrl();
                    $productArray[$key]['product_url'] = $productData->getProductUrl();
                    $productArray[$key]['nspr'] = $productData->getData('nspr');
                    $productArray[$key]['label'] = $productData->getResource()->getAttribute('nspr')->getStoreLabel();
                    $productArray[$key]['nsprContent'] = '<span style="padding: 0px 6px 0px 6px">' . $productArray[$key]['label'] . '</span><span style="background-color: #5b1f07;border-radius: 10px;border: 1px solid #5b1f07;color: white;padding: 0px 6px 0px 6px;">'. $productArray[$key]['nspr'] .'</span>';
                    if (strpos($productData->getProductUrl(), '/products/') !== false) {
                        $urlPath = explode("/", $productData->getProductUrl());
                    }

                }
                /**
                 * For catalog search page [End]
                 */
            } else {
                $productData = $this->productFactory->create()->load($credentials['productID']);
                $productArray[0]['id'] = $credentials['productID'];
                $productArray[0]['name'] = $productData->getName();
                if (array_key_exists('cid', $credentials)) {
                    if ($credentials['cid']) {
                        $productArray[0]['position'] = $this->getPosition($credentials['productID']);
                    }
                }
                $productArray[0]['cmscontent'] = $this->sitHelper->getCmsFilterContent($productData->getData('feature_1_en'));
                $productArray[0]['left_image'] = $this->imageHelper->init($productData, 'small_image')->setImageFile($productData->getSmallImage())->constrainOnly(true)->keepAspectRatio(true)->keepTransparency(true)->keepFrame(false)->resize(100)->getUrl();
                $productArray[0]['right_image'] = $this->imageHelper->init($productData, 'thumbnail')->setImageFile($productData->getThumbnail())->getUrl();
                $productArray[0]['product_url'] = $productData->getProductUrl();
                $productArray[0]['nspr'] = $productData->getData('nspr');
                $productArray[0]['label'] = $productData->getResource()->getAttribute('nspr')->getStoreLabel();
                $productArray[0]['nsprContent'] = '<span style="padding: 0px 6px 0px 6px">' . $productArray[$key]['label'] . '</span><span style="background-color: #5b1f07;border-radius: 10px;border: 1px solid #5b1f07;color: white;padding: 0px 6px 0px 6px;">'. $productArray[$key]['nspr'] .'</span>';
                if (strpos($productData->getProductUrl(), '/products/') !== false) {
                    $urlPath = explode("/", $productData->getProductUrl());
                }

            }
        } else {
            $productList = $this->sitHelper->getProductNameList($credentials['cuk']);
            $credentials = $this->jsonHelper->jsonDecode($productList);
            foreach ($credentials as $key => $value) {
                $productData = $this->productFactory->create()->load($value['id']);
                $productArray[$key]['id'] = $value['id'];
                $productArray[$key]['name'] = $productData->getName();
                if (array_key_exists('cid', $credentials)) {
                    if ($credentials['cid']) {
                        $productArray[$key]['position'] = $this->getPosition($value['id']);
                    }
                }
                $productArray[$key]['cmscontent'] = $this->sitHelper->getCmsFilterContent($productData->getData('feature_1_en'));
                $productArray[$key]['left_image'] = $this->imageHelper->init($productData, 'small_image')->setImageFile($productData->getSmallImage())->constrainOnly(true)->keepAspectRatio(true)->keepTransparency(true)->keepFrame(false)->resize(100)->getUrl();
                $productArray[$key]['right_image'] = $this->imageHelper->init($productData, 'thumbnail')->setImageFile($productData->getThumbnail())->getUrl();
                $productArray[$key]['product_url'] = $productData->getProductUrl();
                $productArray[$key]['nspr'] = $productData->getData('nspr');
                $productArray[$key]['label'] = $productData->getResource()->getAttribute('nspr')->getStoreLabel();
                $productArray[$key]['nsprContent'] = '<span style="padding: 0px 6px 0px 6px">' . $productArray[$key]['label'] . '</span><span style="background-color: #5b1f07;border-radius: 10px;border: 1px solid #5b1f07;color: white;padding: 0px 6px 0px 6px;">'. $productArray[$key]['nspr'] .'</span>';
                if (strpos($productData->getProductUrl(), '/products/') !== false) {
                    $urlPath = explode("/", $productData->getProductUrl());
                }

            }
        }

        if (array_key_exists('cid', $credentials)) {
            if ($credentials['cid']) {
                usort($productArray, function ($item1, $item2) {
                    return $item1['position'] <=> $item2['position'];
                });
            }
        }

        return $resultJson->setData(['product_data' => $productArray]);
    }

    public function getPosition($proId)
    {

        $cId = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
        $catId = $cId['cid'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $select = "SELECT position FROM `catalog_category_product` WHERE  `category_id` = $catId AND `product_id` = $proId";
        $position = $connection->fetchOne($select);
        return $position;
    }

}
