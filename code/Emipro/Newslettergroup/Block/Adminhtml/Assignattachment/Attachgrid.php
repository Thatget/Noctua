<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Block\Adminhtml\Assignattachment;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Extended;

class Attachgrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $collectionFactory;

    /**
     * @var \Emipro\Newslettergroup\Model\Newsletter
     */
    protected $newsLetter;

    /**
     * @var \Emipro\Newslettergroup\Model\Usersubscriber
     */
    protected $userSubscriber;

    /**
     * @var Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $productAttributeRepository;

    /**
     * @var Magento\Customer\Model\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context             $context                    [description]
     * @param \Magento\Backend\Helper\Data                        $backendHelper              [description]
     * @param \Magento\Newsletter\Model\SubscriberFactory         $collectionFactory          [description]
     * @param \Emipro\Newslettergroup\Model\Newsletter            $newsLetter                 [description]
     * @param \Emipro\Newslettergroup\Model\Usersubscriber        $userSubscriber             [description]
     * @param \Magento\Framework\Registry                         $coreRegistry               [description]
     * @param \Magento\Framework\App\ResourceConnection           $resourceConnection         [description]
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository [description]
     * @param \Magento\Customer\Model\GroupFactory                $groupFactory               [description]
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManager               [description]
     * @param array                                               $data                       [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Newsletter\Model\SubscriberFactory $collectionFactory,
        \Emipro\Newslettergroup\Model\Newsletter $newsLetter,
        \Emipro\Newslettergroup\Model\Usersubscriber $userSubscriber,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->newsLetter = $newsLetter;
        $this->userSubscriber = $userSubscriber;
        $this->collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->resourceConnection = $resourceConnection;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->groupFactory = $groupFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('add_subscribers_to_group');
        $this->setDefaultSort('subscriber_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(array('in_category' => 1));
        }
        $this->setSaveParametersInSession(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('subscriber_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('subscriber_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create()->getCollection(); //->addAttributeToSelect("*");

        if ($this->getRequest()->getParam('id')) {
            $product_id = $this->getRequest()->getParam('id');
        } else {
            $groupData = $this->newsLetter->getCollection()->getLastItem();
            $product_id = $groupData->getId() + 1;
//            Optimize Newsletter group page
            /*$product_id = null;
            foreach ($groupData as $key => $valueGroup) {
                $product_id = $valueGroup->getId() + 1;
            }*/
        }
        $tableName = $this->resourceConnection;

//        $protable = $tableName->getTableName('emipro_newsletter_user_subscriber');
        $customer = $tableName->getTableName('customer_entity');
        $customerGroup = $tableName->getTableName('customer_group');
        $store = $tableName->getTableName('store');
        if ($product_id) {
            /**
             * For Subscribers Collection
             */
//            Optimize Newsletter group page
//            $collection->getSelect()->joinLeft(array('prodoctable' => $protable), 'prodoctable.sub_id=main_table.subscriber_id AND prodoctable.group_id=' . $product_id, array('*'));
            /**
             * For Store View Collection
             */
            $collection->getSelect()->joinLeft(array('storeview' => $store), 'main_table.store_id=storeview.store_id', array('name'));
            /**
             * For Customer First Name And Last Name
             */
            $collection->getSelect()->joinLeft(array('customerdata' => $customer), 'customerdata.entity_id=main_table.customer_id', array('firstname', 'lastname', 'group_id'));
            /**
             * For Customer Group
             */
            $collection->getSelect()->joinLeft(array('customergrpdata' => $customerGroup), 'customerdata.group_id=customergrpdata.customer_group_id', array('customer_group_code'));
            /**
             * For Customer Type
             */
            $collection->addSubscriberTypeField();
            $collection->getSelect()->group('main_table.subscriber_id');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $subscriberStatus = [2 => 'Not Activated', 1 => 'Subscribed', 3 => 'Unsubscribed', 4 => 'Unconfirmed'];
        $subscriberType = [1 => 'Guest', 2 => 'Customer'];
        /**
         * Customer Group Collection
         */
        $customerGroup = $this->groupFactory->create()->getCollection();
        $customerGrp = [];
        foreach ($customerGroup as $group) {
            $customerGrp[$group->getCode()] = $group->getCode();
        }
        /**
         * Subscriber Source Collection
         */
        $options = $this->productAttributeRepository->get('newsletter_website')->getOptions();
        $subscriberSource = [];
        foreach ($options as $option) {
            $subscriberSource[$option->getValue()] = $option->getLabel();
        }

        /**
         * Get All Stores Collection
         */
        $stores = $this->storeManager->getStores();
        $storeView = [];
        foreach ($stores as $store) {
            $storeView[$store->getName()] = $store->getName();
        }

        $this->addColumn(
            'in_category',
            [
                'name' => 'in_category',
                'type' => 'checkbox',
                'values' => $this->_getSelectedProducts(),
                'index' => 'subscriber_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction',
            ]
        );
        $this->addColumn(
            'subscriber_id',
            [
                'header' => __('Id'),
                'sortable' => true,
                'index' => 'subscriber_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'subscriber_email',
            [
                'header' => __('Email'),
                'index' => 'subscriber_email',
            ]
        );
        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type',
                'type' => 'options',
                'options' => $subscriberType,
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
            ]
        );
        $this->addColumn(
            'customer_group_code',
            [
                'header' => __('Customer Group'),
                'index' => 'customer_group_code',
                'type' => 'options',
                'options' => $customerGrp,
                'header_css_class' => 'col-group',
                'column_css_class' => 'col-group',
            ]
        );
        $this->addColumn(
            'firstname',
            [
                'header' => __('Customer First Name'),
                'index' => 'firstname',
                'default' => '----',
            ]
        );
        $this->addColumn(
            'lastname',
            [
                'header' => __('Customer Last Name'),
                'index' => 'lastname',
                'default' => '----',
            ]
        );
        $this->addColumn(
            'source_website',
            [
                'header' => __('Subscriber Source'),
                'index' => 'source_website',
                'type' => 'options',
                'options' => $subscriberSource,
                'header_css_class' => 'col-group',
                'column_css_class' => 'col-group',
            ]
        );
        $this->addColumn(
            'subscriber_status',
            [
                'header' => __('Status'),
                'index' => 'subscriber_status',
                'type' => 'options',
                'options' => $subscriberStatus,
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Store View'),
                'index' => 'name',
                'type' => 'options',
                'options' => $storeView,
                'header_css_class' => 'col-store',
                'column_css_class' => 'col-store',
            ]
        );
        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'index' => 'position',
                'type' => 'input',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('newslettergroup/newsletter/grid', ['_current' => true]);
    }
    protected function _getSelectedProducts()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $product_id = $this->getRequest()->getParam('id');
        $products = $this->userSubscriber->getCollection()->addFieldToFilter('group_id', $product_id);

        $prodId = [];
        foreach ($products as $data) {
            $prodId[(int) $data->getSubId()] = array('position' => $data->getSubId());
        }
        return array_keys($prodId);
    }

}
