<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreRepository;
use Mediarocks\ProSlider\Model\SliderFactory;

/**
 * Class Index
 */
class Save extends Action {

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var SliderFactory
	 */
	protected $sliderFactory;

	/**
	 * @var StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var StoreRepository
	 */
	protected $storeRepository;

	/**
	 * @param \Magento\Framework\App\RequestInterface $request         [description]
	 * @param StoreManagerInterface                   $storeManager    [description]
	 * @param Context                                 $context         [description]
	 * @param StoreRepository                         $storeRepository [description]
	 * @param SliderFactory                           $sliderFactory   [description]
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		StoreManagerInterface $storeManager,
		Context $context,
		StoreRepository $storeRepository,
		SliderFactory $sliderFactory
	) {
		$this->storeRepository = $storeRepository;
		$this->storeManager = $storeManager;
		$this->request = $request;
		$this->sliderFactory = $sliderFactory;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Mediarocks_ProSlider::slider');
	}

	/**
	 * save slider data
	 */
	public function execute() {
		// $storeId = (int) $this->getRequest()->getParam('store_id');
		$data = $this->getRequest()->getParams();

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			$params = [];
			/* Added By MJ [For Save Records After Edit Records] [01.04.2019]*/
			if (isset($data['entity_id'])) {
				$sliderInstance = $this->sliderFactory->create()->load($data['entity_id']);
				if (isset($data['store_id'])) {
					if (in_array(0, $data['store_id'])) {
						$stores = $this->storeRepository->getList();
						$storeIdJson = array();
						foreach ($stores as $key => $value) {
							$storeIdJson[] = $value->getId();
						}
						$sliderInstance->setStoreId(json_encode($storeIdJson));
					} else {
						$sliderInstance->setStoreId(json_encode($data['store_id']));
					}
				}
				$sliderInstance->setTitle($data['title']);
				$sliderInstance->setPageType($data['page_type']);
				$sliderInstance->setIsActive($data['is_active']);
				if ($data['page_type'] == 'Custom') {
					if (isset($data['custom_page'])) {
						$data['custom_page'] = serialize(json_encode(explode(',', $data['custom_page'])));
						$sliderInstance->setCustomPage($data['custom_page']);
					}
				}
				if (isset($data['slides'])) {
					$sliderInstance->setSlides($data['slides']);
				} else {
					$this->messageManager->addError(__("Please Select Slides"));
					if (isset($data['back'])) {
						return $resultRedirect->setPath('*/*/edit', ['entity_id' => $sliderInstance->getId(), '_current' => true]);
					}
					return $resultRedirect->setPath('*/*/');
				}
				if ($data['page_type'] == 'CMS') {
					if (isset($data['cms_page'])) {
						$sliderInstance->setCmsPage(serialize(json_encode($data['cms_page'])));
					} else {
						$this->messageManager->addError(__("Please Select CMS Pages"));
						if (isset($data['back'])) {
							return $resultRedirect->setPath('*/*/edit', ['entity_id' => $sliderInstance->getId(), '_current' => true]);
						}
						return $resultRedirect->setPath('*/*/');
					}
				}
				if ($data['page_type'] == 'Product') {
					if (isset($data['sku'])) {
						$sliderInstance->setSku(serialize(json_encode($data['sku'])));
					} else {
						$this->messageManager->addError(__("Please Select Product SKU"));
						if (isset($data['back'])) {
							return $resultRedirect->setPath('*/*/edit', ['entity_id' => $sliderInstance->getId(), '_current' => true]);
						}
						return $resultRedirect->setPath('*/*/');
					}
				}
				if ($data['page_type'] == 'Category') {
					if (isset($data['category'])) {
						$sliderInstance->setCategory(serialize(json_encode($data['category'])));
					} else {
						$this->messageManager->addError(__("Please Select Category"));
						if (isset($data['back'])) {
							return $resultRedirect->setPath('*/*/edit', ['entity_id' => $sliderInstance->getId(), '_current' => true]);
						}
						return $resultRedirect->setPath('*/*/');
					}
				}
				try {
					$sliderInstance->save();
					$this->messageManager->addSuccessMessage(__('Record Updated Successfully.'));
					if ($this->getRequest()->getParam('back')) {
						$params['entity_id'] = $sliderInstance->getId();
						$params['_current'] = true;
						return $resultRedirect->setPath('*/*/edit', $params);
					}
					return $resultRedirect->setPath('*/*/');
				} catch (\Exception $e) {
					$this->messageManager->addErrorMessage($e->getMessage());
					$this->messageManager->addExceptionMessage($e, __('Something went wrong while updating the record.'));
				}
				/* End By MJ [For Save Records After Edit Records] [01.04.2019]*/
			} else {
				$sliderInstance = $this->sliderFactory->create();
				if (isset($data['store_id'])) {
					if (in_array(0, $data['store_id'])) {
						$stores = $this->storeRepository->getList();
						$storeIdJson = array();
						foreach ($stores as $key => $value) {
							$storeIdJson[] = $value->getId();
						}
						$sliderInstance->setStoreId(json_encode($storeIdJson));
					} else {
						$sliderInstance->setStoreId(json_encode($data['store_id']));
					}
				}
				$sliderInstance->setTitle($data['title']);
				$sliderInstance->setPageType($data['page_type']);
				$sliderInstance->setIsActive($data['is_active']);
				if (isset($data['custom_page'])) {
					$data['custom_page'] = serialize(json_encode(explode(',', $data['custom_page'])));
					$sliderInstance->setCustomPage($data['custom_page']);
				}
				if (isset($data['slides'])) {
				    $sliderInstance->setSlides($data['slides']);
//				    $sliderInstance->setSlides(substr($data['slides'], 1));
				} else {
					$this->messageManager->addError(__("Record Not Saved. Please Select Slides"));
					if (isset($data['back'])) {
						return $resultRedirect->setPath('*/*/edit', ['entity_id' => $sliderInstance->getId(), '_current' => true]);
					}
					return $resultRedirect->setPath('*/*/add', $params);
				}
				if ($data['page_type'] == 'CMS') {
					if (isset($data['cms_page'])) {
						$sliderInstance->setCmsPage(serialize(json_encode($data['cms_page'])));
					} else {
						$this->messageManager->addError(__("Please Select CMS Pages"));
						if (isset($data['back'])) {
							return $resultRedirect->setPath('*/*/edit', ['entity_id' => $sliderInstance->getId(), '_current' => true]);
						}
						return $resultRedirect->setPath('*/*/');
					}
				}
				if ($data['page_type'] == 'Product') {
					if (isset($data['sku'])) {
						$sliderInstance->setSku(serialize(json_encode($data['sku'])));
					} else {
						$this->messageManager->addError(__("Please Select Product SKU"));
						return $resultRedirect->setPath('*/*/edit', $params);
					}
				}
				if ($data['page_type'] == 'Category') {
					if (isset($data['category'])) {
						$sliderInstance->setCategory(serialize(json_encode($data['category'])));
					} else {
						$this->messageManager->addError(__("Please Select Category"));
						return $resultRedirect->setPath('*/*/edit', $params);
					}
				}
				// End By MJ [For Dependent Option] [26.03.2019]

				if (empty($data['entity_id'])) {
					$data['entity_id'] = null;
				} else {
					$sliderInstance->load($data['entity_id']);
					$params['entity_id'] = $data['entity_id'];
				}

				// Added By MJ [For Dependent Option] [26.03.2019]
				if (isset($data['category_name'])) {
					$data['category_name'] = serialize($data['category_name']);
					$sliderInstance->setCateogryName($data['category_name']);
				}
				if (isset($data['product_sku'])) {
					$data['product_sku'] = serialize($data['product_sku']);
					$sliderInstance->setSku($data['product_sku']);
				}
				// End By MJ [For Dependent Option] [26.03.2019]

				$this->_eventManager->dispatch(
					'mediarocks_proslider_slider_prepare_save',
					['object' => $this->sliderFactory, 'request' => $this->getRequest()]
				);

				try {
					$sliderInstance->save();
					$this->_getSession()->setFormData(false);
					$this->messageManager->addSuccessMessage(__('Record saved Successfully.'));
					if ($this->getRequest()->getParam('back')) {
						$params['entity_id'] = $sliderInstance->getId();
						$params['_current'] = true;
						return $resultRedirect->setPath('*/*/edit', $params);
					}
					return $resultRedirect->setPath('*/*/');
				} catch (\Exception $e) {
					$this->messageManager->addErrorMessage($e->getMessage());
					$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
				}

				$this->_getSession()->setFormData($this->getRequest()->getPostValue());
				return $resultRedirect->setPath('*/*/edit', $params);
			}
		}
		return $resultRedirect->setPath('*/*/');
	}
}
