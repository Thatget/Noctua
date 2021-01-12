<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [08-04-2019]
 */

namespace Emipro\Newslettergroup\Model\NewsletterSource;

class Customer implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollection;

    /**
     * [__construct description]
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection [description]
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection
    ) {

        $this->groupCollection = $groupCollection;
    }

    public function toOptionArray()
    {
        return $this->groupCollection->create()->toOptionArray();
    }
}
