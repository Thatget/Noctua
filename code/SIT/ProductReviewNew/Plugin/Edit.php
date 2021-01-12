<?php
namespace SIT\ProductReviewNew\Plugin;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Product\Edit
{
    public function execute()
    {
        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $store = $storeManager->getStore($storeId);
        $storeManager->setCurrentStore($store->getCode());
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->productBuilder->build($this->getRequest());

        if (($productId && !$product->getEntityId())) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('This product doesn\'t exist.'));
            return $resultRedirect->setPath('catalog/*/');
        } elseif ($productId === 0) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('Invalid product id. Should be numeric value greater than 0'));
            return $resultRedirect->setPath('catalog/*/');
        }

        $this->_eventManager->dispatch('catalog_product_edit_action', ['product' => $product]);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('catalog_product_' . $product->getTypeId());
        $resultPage->setActiveMenu('Magento_Catalog::catalog_products');
        $resultPage->getConfig()->getTitle()->prepend(__('Products'));
        $resultPage->getConfig()->getTitle()->prepend($product->getName());

        if (!$this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->isSingleStoreMode()
            &&
            ($switchBlock = $resultPage->getLayout()->getBlock('store_switcher'))
        ) {
            $switchBlock->setDefaultStoreName(__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl(
                        'catalog/*/*',
                        ['_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null]
                    )
                );
        }
        /**
         * Code start by : AS [15-5-2019] ['Set Product id in Product edit form.']
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $objectManager->get('\Magento\Framework\Registry')->register('entity_id', $productId);
        $productId = $objectManager->get('\Magento\Framework\Registry')->registry('entity_id');

        setcookie('entity_id', $productId, time() + (3600), "/");

        /**
         * Code end by : AS [15-5-2019] ['Set Product id in Product edit form.']
         */

        return $resultPage;
    }
}
