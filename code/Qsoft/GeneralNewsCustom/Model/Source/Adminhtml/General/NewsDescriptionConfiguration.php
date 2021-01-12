<?php
/**
 * Copyright © Qsoft, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\GeneralNewsCustom\Model\Source\Adminhtml\General;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\ValueSourceInterface;

/**
 * Class NewsDescriptionConfiguration
 *
 * @package Qsoft\GeneralNewsCustom\Model\Source\Adminhtml\General
 */
class NewsDescriptionConfiguration implements ValueSourceInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory
     */
    protected $newsFactory;

    /**
     * NewsTitleConfiguration constructor.
     *
     * @param RequestInterface $request
     * @param \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory $newsFactory
     */
    public function __construct(
        RequestInterface $request,
        \SIT\GeneralNews\Model\ResourceModel\News\CollectionFactory $newsFactory
    ) {
        $this->request = $request;
        $this->newsFactory = $newsFactory;
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue($name)
    {
        $entityId = $this->request->getParam('entity_id');
        if ($entityId) {
            $storeId = (int) $this->request->getParam('store');
            $newsData = $this->newsFactory->create();
            $news = $newsData->setStoreId($storeId)
                ->addFieldToSelect('*')
                ->addFieldToFilter("entity_id", ["eq" => $entityId])
                ->getFirstItem();
            if (!$news->getEntityId()) {
                return "";
            } else {
                return $news->getNewsDesc();
            }
        } else {
            return "";
        }
    }
}
