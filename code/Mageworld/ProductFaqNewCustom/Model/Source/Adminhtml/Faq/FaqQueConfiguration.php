<?php
/**
 * Copyright © Mageworld, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageworld\ProductFaqNewCustom\Model\Source\Adminhtml\Faq;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\ValueSourceInterface;

/**
 * Class FaqQueConfiguration
 *
 * @package Mageworld\ProductFaqNewCustom\Model\Source\Adminhtml\Faq
 */
class FaqQueConfiguration implements ValueSourceInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * FaqQueConfiguration constructor.
     *
     * @param RequestInterface $request
     * @param \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory
     */
    public function __construct(
        RequestInterface $request,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory
    ) {
        $this->request = $request;
        $this->productFaqFactory = $productFaqFactory;
    }

    /**
     * Get value
     *
     * @param string $name
     * @return string
     */
    public function getValue($name)
    {
        $entityId = $this->request->getParam('entity_id');
        if ($entityId) {
            $storeId = (int) $this->request->getParam('store');
            $productfaqnewData = $this->productFaqFactory->create();
            $productfaqnew = $productfaqnewData->setStoreId($storeId)
                ->addFieldToSelect('*')
                ->addFieldToFilter("entity_id", ["eq" => $entityId])
                ->getFirstItem();
            if (!$productfaqnew->getEntityId()) {
                return "";
            } else {
                return $productfaqnew->getFaqQue();
            }
        } else {
            return "";
        }
    }
}
