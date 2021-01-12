<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Code standard by : MD [06-07-2019]
 */

namespace SIT\MainAdmin\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use SIT\ProductCompatibility\Model\Product as SitCompProduct;
use SIT\ProductCompatibility\Model\ProductFactory as SitCompProductFactory;
use SIT\ProductCompatibility\Model\ResourceModel\Product\CollectionFactory as SitCompProCollFactory;
use SIT\ProductFaqNew\Model\Product as SitFaqProduct;
use SIT\ProductFaqNew\Model\ProductFactory as SitFaqProductFactory;
use SIT\ProductFaqNew\Model\ResourceModel\Product\CollectionFactory as SitFaqProCollFactory;
use SIT\ProductReviewNew\Model\Product as SitReviewProduct;
use SIT\ProductReviewNew\Model\ProductFactory as SitReviewProductFactory;
use SIT\ProductReviewNew\Model\ResourceModel\Product\CollectionFactory as SitReviewProCollFactory;
use SIT\ProductTechNew\Model\Product as SitTechProduct;
use SIT\ProductTechNew\Model\ProductFactory as SitTechProductFactory;
use SIT\ProductTechNew\Model\ResourceModel\Product\CollectionFactory as SitTechProCollFactory;
use SIT\ProductVideoNew\Model\Product as SitVideoProduct;
use SIT\ProductVideoNew\Model\ProductFactory as SitVideoProductFactory;
use SIT\ProductVideoNew\Model\ResourceModel\Product\CollectionFactory as SitVideoProCollFactory;
use Mediarocks\ProSlider\Model\SliderFactory;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Catalog\Controller\Adminhtml\Product\Save {
	/**
	 * @var Initialization\Helper
	 */
	protected $initializationHelper;

	/**
	 * @var \Magento\Catalog\Model\Product\Copier
	 */
	protected $productCopier;

	/**
	 * @var \Magento\Catalog\Model\Product\TypeTransitionManager
	 */
	protected $productTypeManager;

	/**
	 * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
	 */
	protected $categoryLinkManagement;

	/**
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository;

	/**
	 * @var DataPersistorInterface
	 */
	protected $dataPersistor;

	/**
	 * @var StoreManagerInterface
	 */
	private $storeManager;

	/**
	 * @var \Magento\Framework\Escaper|null
	 */
	private $escaper;

	/**
	 * @var null|\Psr\Log\LoggerInterface
	 */
	private $logger;

	/**
	 * @var SitCompProduct
	 */
	protected $sitCompProduct;

	/**
	 * @var SitCompProductFactory
	 */
	protected $sitCompProductFactory;

	/**
	 * @var SitFaqProduct
	 */
	protected $sitFaqProduct;

	/**
	 * @var SitFaqProductFactory
	 */
	protected $sitFaqProductFactory;

	/**
	 * @var SitReviewProduct
	 */
	protected $sitReviewProduct;

	/**
	 * @var SitReviewProductFactory
	 */
	protected $sitReviewProductFactory;

	/**
	 * @var SitTechProduct
	 */
	protected $sitTechProduct;

	/**
	 * @var SitTechProductFactory
	 */
	protected $sitTechProductFactory;

	/**
	 * @var SitVideoProduct
	 */
	protected $sitVideoProduct;

	/**
	 * @var SitVideoProductFactory
	 */
	protected $sitVideoProductFactory;

	/**
	 * @var SitCompProCollFactory
	 */
	protected $sitCompProCollFactory;

	/**
	 * @var SitFaqProCollFactory
	 */
	protected $sitFaqProCollFactory;

	/**
	 * @var SitReviewProCollFactory
	 */
	protected $sitReviewProCollFactory;

	/**
	 * @var SitTechProCollFactory
	 */
	protected $sitTechProCollFactory;

	/**
	 * @var SitVideoProCollFactory
	 */
	protected $sitVideoProCollFactory;
    /**
     * @var SliderFactory
     */
    protected $sliderFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context                                 $context                 [description]
	 * @param Product\Builder                                                     $productBuilder          [description]
	 * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper    [description]
	 * @param \Magento\Catalog\Model\Product\Copier                               $productCopier           [description]
	 * @param \Magento\Catalog\Model\Product\TypeTransitionManager                $productTypeManager      [description]
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface                     $productRepository       [description]
	 * @param \Magento\Framework\Escaper|null                                     $escaper                 [description]
	 * @param \Psr\Log\LoggerInterface|null                                       $logger                  [description]
	 * @param SitCompProduct                                                      $sitCompProduct          [description]
	 * @param SitCompProductFactory                                               $sitCompProductFactory   [description]
	 * @param SitFaqProduct                                                       $sitFaqProduct           [description]
	 * @param SitFaqProductFactory                                                $sitFaqProductFactory    [description]
	 * @param SitReviewProduct                                                    $sitReviewProduct        [description]
	 * @param SitReviewProductFactory                                             $sitReviewProductFactory [description]
	 * @param SitTechProduct                                                      $sitTechProduct          [description]
	 * @param SitTechProductFactory                                               $sitTechProductFactory   [description]
	 * @param SitVideoProduct                                                     $sitVideoProduct         [description]
	 * @param SitVideoProductFactory                                              $sitVideoProductFactory  [description]
	 * @param SitCompProCollFactory                                               $sitCompProCollFactory   [description]
	 * @param SitFaqProCollFactory                                                $sitFaqProCollFactory    [description]
	 * @param SitReviewProCollFactory                                             $sitReviewProCollFactory [description]
	 * @param SitTechProCollFactory                                               $sitTechProCollFactory   [description]
	 * @param SitVideoProCollFactory                                              $sitVideoProCollFactory  [description]
     * @param SliderFactory                                                       $sliderFactory           [description]
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		Product\Builder $productBuilder,
		\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
		\Magento\Catalog\Model\Product\Copier $productCopier,
		\Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Framework\Escaper $escaper = null,
		\Psr\Log\LoggerInterface $logger = null,
		SitCompProduct $sitCompProduct,
		SitCompProductFactory $sitCompProductFactory,
		SitFaqProduct $sitFaqProduct,
		SitFaqProductFactory $sitFaqProductFactory,
		SitReviewProduct $sitReviewProduct,
		SitReviewProductFactory $sitReviewProductFactory,
		SitTechProduct $sitTechProduct,
		SitTechProductFactory $sitTechProductFactory,
		SitVideoProduct $sitVideoProduct,
		SitVideoProductFactory $sitVideoProductFactory,
		SitCompProCollFactory $sitCompProCollFactory,
		SitFaqProCollFactory $sitFaqProCollFactory,
		SitReviewProCollFactory $sitReviewProCollFactory,
		SitTechProCollFactory $sitTechProCollFactory,
		SitVideoProCollFactory $sitVideoProCollFactory,
        SliderFactory $sliderFactory
	) {
		$this->initializationHelper = $initializationHelper;
		$this->productCopier = $productCopier;
		$this->productTypeManager = $productTypeManager;
		$this->productRepository = $productRepository;
		parent::__construct($context,
			$productBuilder,
			$initializationHelper,
			$productCopier,
			$productTypeManager,
			$productRepository,
			$escaper,
			$logger);
		$this->escaper = $escaper ?? $this->_objectManager->get(\Magento\Framework\Escaper::class);
		$this->logger = $logger ?? $this->_objectManager->get(\Psr\Log\LoggerInterface::class);
		$this->sitCompProduct = $sitCompProduct;
		$this->sitCompProductFactory = $sitCompProductFactory;
		$this->sitFaqProduct = $sitFaqProduct;
		$this->sitFaqProductFactory = $sitFaqProductFactory;
		$this->sitReviewProduct = $sitReviewProduct;
		$this->sitReviewProductFactory = $sitReviewProductFactory;
		$this->sitTechProduct = $sitTechProduct;
		$this->sitTechProductFactory = $sitTechProductFactory;
		$this->sitVideoProduct = $sitVideoProduct;
		$this->sitVideoProductFactory = $sitVideoProductFactory;
		$this->sitCompProCollFactory = $sitCompProCollFactory;
		$this->sitFaqProCollFactory = $sitFaqProCollFactory;
		$this->sitReviewProCollFactory = $sitReviewProCollFactory;
		$this->sitTechProCollFactory = $sitTechProCollFactory;
		$this->sitVideoProCollFactory = $sitVideoProCollFactory;
		$this->sliderFactory = $sliderFactory;
	}

	/**
	 * Save product action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$storeId = $this->getRequest()->getParam('store', 0);
		$store = $this->getStoreManager()->getStore($storeId);
		$this->getStoreManager()->setCurrentStore($store->getCode());
		$redirectBack = $this->getRequest()->getParam('back', false);
		$productId = $this->getRequest()->getParam('id');
		$resultRedirect = $this->resultRedirectFactory->create();
		$data = $this->getRequest()->getPostValue();
		$productAttributeSetId = $this->getRequest()->getParam('set');
		$productTypeId = $this->getRequest()->getParam('type');
		if ($data) {
			try {
				$product = $this->initializationHelper->initialize(
					$this->productBuilder->build($this->getRequest())
				);
				$this->productTypeManager->processProduct($product);
				if (isset($data['product'][$product->getIdFieldName()])) {
					throw new \Magento\Framework\Exception\LocalizedException(
						__('The product was unable to be saved. Please try again.')
					);
				}

				$originalSku = $product->getSku();
				$canSaveCustomOptions = $product->getCanSaveCustomOptions();
				$product->setTypeId($productTypeId);
				$product->save();
				$this->handleImageRemoveError($data, $product->getId());
				$this->getCategoryLinkManagement()->assignProductToCategories(
					$product->getSku(),
					$product->getCategoryIds()
				);
				$productId = $product->getEntityId();
				$productAttributeSetId = $product->getAttributeSetId();
				$productTypeId = $product->getTypeId();
				$extendedData = $data;
				$extendedData['can_save_custom_options'] = $canSaveCustomOptions;
				$this->copyToStores($extendedData, $productId);
				/*Changed by MD for save custom tab Data[START][06-07-2019]*/
				//For Product Compatibilities[START]
				if (isset($data['comp_product'])
					&& is_string($data['comp_product'])) {
					$compIds = (array) json_decode($data['comp_product']);
					$this->insertCompIds($productId, $compIds, null, '');
				}
				//For Product Compatibilities[END]
				//For Product FAQs[START]
				if (isset($data['faq_product'])
					&& is_string($data['faq_product'])) {
					$faqIds = (array) json_decode($data['faq_product']);
					$this->insertFaqIds($productId, $faqIds, null, '');
				}
				//For Product FAQs[END]
				//For Product Reviews[START]
				if (isset($data['review_product'])
					&& is_string($data['review_product'])) {
					$reviewIds = (array) json_decode($data['review_product']);
					$this->insertReviewIds($productId, $reviewIds, null, '');
				}
				//For Product Reviews[END]
				//For Product Technologies[START]
				if (isset($data['tech_product'])
					&& is_string($data['tech_product'])) {
					$techIds = (array) json_decode($data['tech_product']);
					$this->insertTechIds($productId, $techIds, null, '');
				}
				//For Product Technologies[END]
				//For Product Videos[START]
				if (isset($data['video_product'])
					&& is_string($data['video_product'])) {
					$videoIds = (array) json_decode($data['video_product']);
					$this->insertVideoIds($productId, $videoIds, null, '');
				}
				//For Product Videos[END]
				/*Changed by MD for save custom tab Data[END][06-07-2019]*/
				$this->messageManager->addSuccessMessage(__('You saved the product.'));
				$this->getDataPersistor()->clear('catalog_product');
				if ($product->getSku() != $originalSku) {
					$this->messageManager->addNoticeMessage(
						__(
							'SKU for product %1 has been changed to %2.',
							$this->escaper->escapeHtml($product->getName()),
							$this->escaper->escapeHtml($product->getSku())
						)
					);
				}
				$this->_eventManager->dispatch(
					'controller_action_catalog_product_save_entity_after',
					['controller' => $this, 'product' => $product]
				);

				if ($redirectBack === 'duplicate') {
					$product->unsetData('quantity_and_stock_status');
					$newProduct = $this->productCopier->copy($product);
					/*Changed by MD for save & duplicate custom tab Data[START][06-07-2019]*/
					$comp_collection = $this->sitCompProCollFactory->create()->addFieldToFilter("product_id", ["eq" => $productId]);
					$faq_collection = $this->sitFaqProCollFactory->create()->addFieldToFilter("product_id", ["eq" => $productId]);
					//$review_collection = $this->sitReviewProCollFactory->create()->addFieldToFilter("product_id", ["eq" => $productId]);
					$tech_collection = $this->sitTechProCollFactory->create()->addFieldToFilter("product_id", ["eq" => $productId]);
					$video_collection = $this->sitVideoProCollFactory->create()->addFieldToFilter("product_id", ["eq" => $productId]);
					/*
					 * Duplicate Banners <Phillip>
					 */
                    //$banners = $this->sliderFactory->create()->getCollection()->addFieldToFilter("sku", ['like' => '%' . $originalSku . '%']);
                    $bannercollection = $this->sliderFactory->create()->getCollection()
                        ->addAttributeToSelect(['sku', 'title', 'page_type'])
                        ->addAttributeToFilter('page_type', ['eq', 'product']);
                    foreach ($bannercollection as $value) {
                        $sliderSku = json_decode(unserialize($value->getSku()));
                        if (in_array($originalSku, $sliderSku)) {
                            $sliderSku[] = $newProduct->getSku();
                            $value->setSku(serialize(json_encode($sliderSku)));
                            $value->save();
                            break;
                        }
                    }
                    /*
                     * End Duplicate Banners
                     */
					/*Changed by MD for save & duplicate custom tab Data[START][31-07-2019]*/
					//For Product Compatibilities[START]
					if (isset($data['comp_product'])
						&& is_string($data['comp_product'])) {
						$compIds = (array) json_decode($data['comp_product']);
						$this->insertCompIds($newProduct->getId(), $compIds, null, 'duplicate');
					} else {
						if (count($comp_collection) > 0) {
							$compIds = [];
							$count = 0;
							foreach ($comp_collection as $key => $value) {
								$compIds[$value['productcompatibility_id']] = $value['position'];
							}
							$this->insertCompIds($newProduct->getId(), $compIds, $comp_collection, 'duplicate');
						}
					}
					//For Product Compatibilities[END]
					//For Product FAQs[START]
					if (isset($data['faq_product'])
						&& is_string($data['faq_product'])) {
						$faqIds = (array) json_decode($data['faq_product']);
						$this->insertFaqIds($newProduct->getId(), $faqIds, null, 'duplicate');
					} else {
						if (count($faq_collection) > 0) {
							$faqIds = [];
							$count = 0;
							foreach ($faq_collection as $key => $value) {
								$faqIds[$value['productfaq_id']] = $value['position'];
							}
							$this->insertFaqIds($newProduct->getId(), $faqIds, $faq_collection, 'duplicate');
						}
					}
					//For Product FAQs[END]
					//For Product Reviews[START]
//					if (isset($data['review_product'])
//						&& is_string($data['review_product'])) {
//						$reviewIds = (array) json_decode($data['review_product']);
//						$this->insertReviewIds($newProduct->getId(), $reviewIds, null, 'duplicate');
//					} else {
//						if (count($review_collection) > 0) {
//							$reviewIds = [];
//							$count = 0;
//							foreach ($review_collection as $key => $value) {
//								$reviewIds[$value['productreview_id']] = $value['position'];
//							}
//							$this->insertReviewIds($newProduct->getId(), $reviewIds, $review_collection, 'duplicate');
//						}
//					}
					//For Product Reviews[END]
					//For Product Technologies[START]
					if (isset($data['tech_product'])
						&& is_string($data['tech_product'])) {
						$techIds = (array) json_decode($data['tech_product']);
						$this->insertTechIds($newProduct->getId(), $techIds, null, 'duplicate');
					} else {
						if (count($tech_collection) > 0) {
							$techIds = [];
							$count = 0;
							foreach ($tech_collection as $key => $value) {
								$techIds[$value['producttechnology_id']] = $value['position'];
							}
							$this->insertTechIds($newProduct->getId(), $techIds, $tech_collection, 'duplicate');
						}
					}
					//For Product Technologies[END]
					//For Product Videos[START]
					if (isset($data['video_product'])
						&& is_string($data['video_product'])) {
						$videoIds = (array) json_decode($data['video_product']);
						$this->insertVideoIds($newProduct->getId(), $videoIds, null, 'duplicate');
					} else {
						if (count($video_collection) > 0) {
							$videoIds = [];
							$count = 0;
							foreach ($video_collection as $key => $value) {
								$videoIds[$value['productvideo_id']] = $value['position'];
							}
							$this->insertVideoIds($newProduct->getId(), $videoIds, $video_collection, 'duplicate');
						}
					}
					//For Product Videos[END]
					/*Changed by MD for save & duplicate custom tab Data[END][31-07-2019]*/
					$this->messageManager->addSuccessMessage(__('You duplicated the product.'));
				}
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->logger->critical($e);
				$this->messageManager->addExceptionMessage($e);
				$data = isset($product) ? $this->persistMediaData($product, $data) : $data;
				$this->getDataPersistor()->set('catalog_product', $data);
				$redirectBack = $productId ? true : 'new';
			} catch (\Exception $e) {
				$this->logger->critical($e);
				$this->messageManager->addErrorMessage($e->getMessage());
				$data = isset($product) ? $this->persistMediaData($product, $data) : $data;
				$this->getDataPersistor()->set('catalog_product', $data);
				$redirectBack = $productId ? true : 'new';
			}
		} else {
			$resultRedirect->setPath('catalog/*/', ['store' => $storeId]);
			$this->messageManager->addErrorMessage('No data to save');
			return $resultRedirect;
		}

		if ($redirectBack === 'new') {
			$resultRedirect->setPath(
				'catalog/*/new',
				['set' => $productAttributeSetId, 'type' => $productTypeId]
			);
		} elseif ($redirectBack === 'duplicate' && isset($newProduct)) {
			$resultRedirect->setPath(
				'catalog/*/edit',
				['id' => $newProduct->getEntityId(), 'back' => null, '_current' => true]
			);
		} elseif ($redirectBack) {
			$resultRedirect->setPath(
				'catalog/*/edit',
				['id' => $productId, '_current' => true, 'set' => $productAttributeSetId]
			);
		} else {
			$resultRedirect->setPath('catalog/*/', ['store' => $storeId]);
		}
		return $resultRedirect;
	}

	/**
	 * Notify customer when image was not deleted in specific case.
	 *
	 * TODO: temporary workaround must be eliminated in MAGETWO-45306
	 *
	 * @param array $postData
	 * @param int $productId
	 * @return void
	 */
	private function handleImageRemoveError($postData, $productId) {
		if (isset($postData['product']['media_gallery']['images'])) {
			$removedImagesAmount = 0;
			foreach ($postData['product']['media_gallery']['images'] as $image) {
				if (!empty($image['removed'])) {
					$removedImagesAmount++;
				}
			}
			if ($removedImagesAmount) {
				$expectedImagesAmount = count($postData['product']['media_gallery']['images']) - $removedImagesAmount;
				$product = $this->productRepository->getById($productId);
				$images = $product->getMediaGallery('images');
				if (is_array($images) && $expectedImagesAmount != count($images)) {
					$this->messageManager->addNoticeMessage(
						__('The image cannot be removed as it has been assigned to the other image role')
					);
				}
			}
		}
	}

	/**
	 * Do copying data to stores
	 *
	 * If the 'copy_from' field is not specified in the input data,
	 * the store fallback mechanism will automatically take the admin store's default value.
	 *
	 * @param array $data
	 * @param int $productId
	 * @return void
	 */
	protected function copyToStores($data, $productId) {
		if (!empty($data['product']['copy_to_stores'])) {
			foreach ($data['product']['copy_to_stores'] as $websiteId => $group) {
				if (isset($data['product']['website_ids'][$websiteId])
					&& (bool) $data['product']['website_ids'][$websiteId]) {
					foreach ($group as $store) {
						if (isset($store['copy_from'])) {
							$copyFrom = $store['copy_from'];
							$copyTo = (isset($store['copy_to'])) ? $store['copy_to'] : 0;
							if ($copyTo) {
								$this->_objectManager->create(\Magento\Catalog\Model\Product::class)
									->setStoreId($copyFrom)
									->load($productId)
									->setStoreId($copyTo)
									->setCanSaveCustomOptions($data['can_save_custom_options'])
									->setCopyFromView(true)
									->save();
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Get categoryLinkManagement in a backward compatible way.
	 *
	 * @return \Magento\Catalog\Api\CategoryLinkManagementInterface
	 */
	private function getCategoryLinkManagement() {
		if (null === $this->categoryLinkManagement) {
			$this->categoryLinkManagement = \Magento\Framework\App\ObjectManager::getInstance()
				->get(\Magento\Catalog\Api\CategoryLinkManagementInterface::class);
		}
		return $this->categoryLinkManagement;
	}

	/**
	 * Get storeManager in a backward compatible way.
	 *
	 * @return StoreManagerInterface
	 * @deprecated 101.0.0
	 */
	private function getStoreManager() {
		if (null === $this->storeManager) {
			$this->storeManager = \Magento\Framework\App\ObjectManager::getInstance()
				->get(\Magento\Store\Model\StoreManagerInterface::class);
		}
		return $this->storeManager;
	}

	/**
	 * Retrieve data persistor
	 *
	 * @return DataPersistorInterface|mixed
	 * @deprecated 101.0.0
	 */
	protected function getDataPersistor() {
		if (null === $this->dataPersistor) {
			$this->dataPersistor = $this->_objectManager->get(DataPersistorInterface::class);
		}

		return $this->dataPersistor;
	}

	/**
	 * Persist media gallery on error, in order to show already saved images on next run.
	 *
	 * @param ProductInterface $product
	 * @param array $data
	 * @return array
	 */
	private function persistMediaData(ProductInterface $product, array $data) {
		$mediaGallery = $product->getData('media_gallery');
		if (!empty($mediaGallery['images'])) {
			foreach ($mediaGallery['images'] as $key => $image) {
				if (!isset($image['new_file'])) {
					//Remove duplicates.
					unset($mediaGallery['images'][$key]);
				}
			}
			$data['product']['media_gallery'] = $mediaGallery;
			$fields = [
				'image',
				'small_image',
				'thumbnail',
				'swatch_image',
			];
			foreach ($fields as $field) {
				$data['product'][$field] = $product->getData($field);
			}
		}

		return $data;
	}
	/*Changed by MD for save custom tab Data[START][06-07-2019]*/
	//For Product Compatibilities[START]
	private function insertCompIds($proId, $compIds, $collection, $duplicate) {
		$tempSitCompProduct = $this->sitCompProduct;
		if ($collection == null) {
			$collection = $tempSitCompProduct->getCollection()->addFieldToFilter("product_id", ["eq" => $proId]);
		}
		if ($duplicate != 'duplicate') {
			foreach ($collection->getData() as $value) {
				$tempSitCompProduct->load($value["rel_id"]);
				$tempSitCompProduct->delete();
				$tempSitCompProduct->unsetData();
			}
		}
		try {
			foreach ($compIds as $key => $value) {
				/**
				 * Set position value null when position field value blank : MD [START][26-03-2019]
				 */
				if ($value != "" && $value >= 0) {
					$fields = ["productcompatibility_id" => $key, "product_id" => $proId, "position" => $value];
				} else {
					$fields = ["productcompatibility_id" => $key, "product_id" => $proId, "position" => 0];
				}
				/**
				 * Set position value null when position field value blank : MD [END][26-03-2019]
				 */
				$this->insertComp($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}

	private function insertComp($productArray) {
		$save = $this->sitCompProductFactory->create();
		$save->setData($productArray);
		$save->save();
	}
	//For Product Compatibilities[END]
	//For Product FAQs[START]
	private function insertFaqIds($proId, $faqIds, $collection, $duplicate) {
		$tempSitFaqProduct = $this->sitFaqProduct;
		if ($collection == null) {
			$collection = $tempSitFaqProduct->getCollection()->addFieldToFilter("product_id", ["eq" => $proId]);
		}
		if ($duplicate != 'duplicate') {
			foreach ($collection->getData() as $value) {
				$tempSitFaqProduct->load($value["rel_id"]);
				$tempSitFaqProduct->delete();
				$tempSitFaqProduct->unsetData();
			}
		}
		try {
			foreach ($faqIds as $key => $value) {
				/**
				 * Set position value null when position field value blank : MD [START][26-03-2019]
				 */
				if ($value != "" && $value >= 0) {
					$fields = ["productfaq_id" => $key, "product_id" => $proId, "position" => $value];
				} else {
					$fields = ["productfaq_id" => $key, "product_id" => $proId, "position" => 0];
				}
				/**
				 * Set position value null when position field value blank : MD [END][26-03-2019]
				 */
				$this->insertFaq($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}
	private function insertFaq($productArray) {
		$save = $this->sitFaqProductFactory->create();
		$save->setData($productArray);
		$save->save();
	}
	//For Product FAQs[END]
	//For Product Reviews[START]
	private function insertReviewIds($proId, $reviewIds, $collection, $duplicate) {
		$tempSitReviewProduct = $this->sitReviewProduct;
		if ($collection == null) {
			$collection = $tempSitReviewProduct->getCollection()->addFieldToFilter("product_id", ["eq" => $proId]);
		}
		if ($duplicate != 'duplicate') {
			foreach ($collection->getData() as $value) {
				$tempSitReviewProduct->load($value["rel_id"]);
				$tempSitReviewProduct->delete();
				$tempSitReviewProduct->unsetData();
			}
		}
		try {
			foreach ($reviewIds as $key => $value) {
				/**
				 * Set position value null when position field value blank : MD [START][26-03-2019]
				 */
				if ($value != "" && $value >= 0) {
					$fields = ["productreview_id" => $key, "product_id" => $proId, "position" => $value];
				} else {
					$fields = ["productreview_id" => $key, "product_id" => $proId, "position" => 0];
				}
				/**
				 * Set position value null when position field value blank : MD [END][26-03-2019]
				 */
				$this->insertReview($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}
	private function insertReview($productArray) {
		$save = $this->sitReviewProductFactory->create();
		$save->setData($productArray);
		$save->save();
	}
	//For Product Reviews[END]
	//For Product Technologies[START]
	private function insertTechIds($proId, $techIds, $collection, $duplicate) {
		$tempSitTechProduct = $this->sitTechProduct;
		if ($collection == null) {
			$collection = $tempSitTechProduct->getCollection()->addFieldToFilter("product_id", ["eq" => $proId]);
		}
		if ($duplicate != 'duplicate') {
			foreach ($collection->getData() as $value) {
				$tempSitTechProduct->load($value["rel_id"]);
				$tempSitTechProduct->delete();
				$tempSitTechProduct->unsetData();
			}
		}
		try {
			foreach ($techIds as $key => $value) {
				/**
				 * Set position value null when position field value blank : MD [START][26-03-2019]
				 */
				if ($value != "" && $value >= 0) {
					$fields = ["producttechnology_id" => $key, "product_id" => $proId, "position" => $value];
				} else {
					$fields = ["producttechnology_id" => $key, "product_id" => $proId, "position" => 0];
				}
				/**
				 * Set position value null when position field value blank : MD [END][26-03-2019]
				 */
				$this->insertTechnology($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}
	private function insertTechnology($productArray) {
		$save = $this->sitTechProductFactory->create();
		$save->setData($productArray);
		$save->save();
	}
	//For Product Technologies[END]
	//For Product Videos[START]
	private function insertVideoIds($proId, $videoIds, $collection, $duplicate) {
		$tempSitVideoProduct = $this->sitVideoProduct;
		if ($collection == null) {
			$collection = $tempSitVideoProduct->getCollection()->addFieldToFilter("product_id", ["eq" => $proId]);
		}
		if ($duplicate != 'duplicate') {
			foreach ($collection->getData() as $value) {
				$tempSitVideoProduct->load($value["rel_id"]);
				$tempSitVideoProduct->delete();
				$tempSitVideoProduct->unsetData();
			}
		}
		try {
			foreach ($videoIds as $key => $value) {
				/**
				 * Set position value null when position field value blank : MD [START][26-03-2019]
				 */
				if ($value != "" && $value >= 0) {
					$fields = ["productvideo_id" => $key, "product_id" => $proId, "position" => $value];
				} else {
					$fields = ["productvideo_id" => $key, "product_id" => $proId, "position" => 0];
				}
				/**
				 * Set position value null when position field value blank : MD [END][26-03-2019]
				 */
				$this->insertVideo($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}
	private function insertVideo($productArray) {
		$save = $this->sitVideoProductFactory->create();
		$save->setData($productArray);
		$save->save();
	}
	//For Product Videos[END]
	/*Changed by MD for save custom tab Data[END][06-07-2019]*/
}
