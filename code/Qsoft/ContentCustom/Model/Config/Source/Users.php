<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qsoft\ContentCustom\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class Users implements \Magento\Framework\Option\ArrayInterface
{
    protected $userCollectionFactory;
    public function __construct(
    \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
    )
    {
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $users = $this->userCollectionFactory->create();
        $result = [];
        foreach($users as $user)
        {
            $result[] = ['value' => $user->getId(), 'label' => $user->getUsername()];
        }
        return $result;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $users = $this->userCollectionFactory->create();
        $result = [];
        foreach($users as $user)
        {
            $result[$user->getId()] = $user->getUsername();
        }
        return $result;
    }
}
