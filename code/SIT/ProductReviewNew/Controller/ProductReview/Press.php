<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductReviewNew\Controller\ProductReview;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class Press extends \Magento\Framework\App\Action\Action
{
    const CATEGORY_URL_KEY = 'products';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $readDirectory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Magento\Framework\App\Action\Context                          $context                  [description]
     * @param \Magento\Framework\Json\Helper\Data                            $jsonHelper               [description]
     * @param \Magento\Catalog\Model\CategoryFactory                         $categoryFactory          [description]
     * @param \Magento\Framework\Filesystem\Driver\File                      $readDirectory            [description]
     * @param \Magento\Framework\Filesystem                                  $filesystem               [description]
     * @param \Magento\Framework\App\Config\ScopeConfigInterface             $scopeConfig              [description]
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory [description]
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Filesystem\Driver\File $readDirectory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->categoryFactory = $categoryFactory;
        $this->readDirectory = $readDirectory;
        $this->filesystem = $filesystem;
        $this->scopeConfig = $scopeConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function execute()
    {
        if ($this->getRequest()->getContent()) {
            $paramArray = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
            if (array_key_exists('press_collection', $paramArray)) {
                return $this->getPressCollection();
            }
        }
    }
    /**
     * Prepare collection for press image
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getPressCollection()
    {
        $_subcategories = [];
        $categoryData = [];
        $allproducts = [];
        $productData = [];
        $productCategoryName = [];

        $getDirectory = $this->readDirectory;
        $pubPath = $this->filesystem->getDirectoryRead(DirectoryList::PUB)->getAbsolutePath();
        $baseUrlWtihoutCode = $this->scopeConfig->getValue("web/unsecure/base_url");

        $category_load = $this->categoryFactory->create()->loadByAttribute('url_key', self::CATEGORY_URL_KEY);
        $subcategories = $category_load->getChildrenCategories();
        foreach ($subcategories as $_subcategory) {
            $_subcategory = $this->categoryFactory->create()->load($_subcategory->getId());
            if ($_subcategory->getData('faq_list_position')) {
                $_subcategories[$_subcategory->getData('faq_list_position')] = $_subcategory;
            }
        }
        ksort($_subcategories);
        $count = 0;
        foreach ($_subcategories as $key => $_category) {
            $products = $this->productCollectionFactory->create()
                ->addCategoryFilter($_category)
                ->addAttributeToSelect(['name', 'press_images'])
                ->addAttributeToFilter('press_images', ['neq' => ''])
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $products->addAttributeToSort('position', 'ASC');

            if ($products->getSize() > 0) {
                $categoryData[$_category->getId()] = $_category->getName();
                foreach ($products as $product) {
                    $productName[$count]['id'] = $product->getId();
                    $productName[$count]['name'] = $product->getName();
                    $productCategoryName[$_category->getId()][$count] = ['id' => $product->getId(), 'name' => $product->getName()];
                    $productData[$_category->getName()][$count][$product->getId()]['name'] = $product->getName();
                    $press_images = $product->getPressImages();

                    /**
                     * Check if path is exists or not
                     */
                    if (is_dir($pubPath . $press_images . '/thumbs/')) {
                        // Get all files from directory
                        $images = $getDirectory->readDirectory($pubPath . $press_images . '/thumbs/');
                        foreach ($images as $key => $image) {
                            $image = explode("/", $image);
                            $image = end($image);
                            $imageUrl = $baseUrlWtihoutCode . "pub/" . $press_images . "/thumbs/" . $image;

                            // Set thumbnail image
                            $productData[$_category->getName()][$count][$product->getId()][$key]['thumbs'] = $imageUrl;

                            $big_image_url = $baseUrlWtihoutCode . 'pub/' . $press_images . '/' . pathinfo($image, PATHINFO_FILENAME);
                            $big_image = $pubPath . $press_images . '/' . pathinfo($image, PATHINFO_FILENAME);
                            $jpg_img = $big_image . '.jpg';
                            $bigImagesArray = [];
                            if (file_exists($jpg_img)) {
                                $bigImagesArray['jpg'] = $big_image_url . '.jpg';
                                // Set jpg image
                                $productData[$_category->getName()][$count][$product->getId()][$key]["jpg"] = $big_image_url . '.jpg';
                            }
                            $jpeg_img = $big_image . '.jpeg';
                            if (file_exists($jpeg_img)) {
                                $bigImagesArray['jpeg'] = $big_image_url . '.jpeg';
                                // Set jpeg image
                                $productData[$_category->getName()][$count][$product->getId()][$key]["jpg"] = $big_image_url . '.jpeg';
                            }
                            $png_img = $big_image . '.png';
                            if (file_exists($png_img)) {
                                // Set png image
                                $bigImagesArray['png'] = $big_image_url . '.png';
                                $productData[$_category->getName()][$count][$product->getId()][$key]["png"] = $big_image_url . '.png';
                            }
                        }
                    }
                    $count++;
                }
            }
        }

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'application/json');
        $response->setContents($this->jsonHelper->jsonEncode(["category" => $categoryData, "product" => $productName, "collection" => $productData, "category_products" => $productCategoryName]));
        return $response;
    }
}
