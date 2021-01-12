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
class Roles implements \Magento\Framework\Option\ArrayInterface
{
    protected $roleCollectionFactory;
    public function __construct(
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory
    )
    {
        $this->roleCollectionFactory = $roleCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $roles = $this->roleCollectionFactory->create();
        $result = [];
        foreach($roles as $role)
        {
            $result[] = ['value' => $role->getId(), 'label' => $role->getRoleName()];
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
        $roles = $this->roleCollectionFactory->create();
        $result = [];
        foreach($roles as $role)
        {
            $result[$role->getId()] = $role->getRoleName();
        }
        return $result;
    }
}
