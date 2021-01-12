<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Ui\Component\Listing\Column;

use Emipro\Newslettergroup\Model\Usersubscriber;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Subscribers extends Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var Usersubscriber
     */
    protected $userSubscriberModel;

    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $uiComponentFactory;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $collectionFactory;

    const URL_PATH_EDIT = 'newslettergroup/newsletter/edit';

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context             [description]
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory  [description]
     * @param \Magento\Framework\UrlInterface                              $urlBuilder          [description]
     * @param \Magento\Customer\Api\GroupRepositoryInterface               $groupRepository     [description]
     * @param Usersubscriber                                               $userSubscriberModel [description]
     * @param \Magento\Newsletter\Model\SubscriberFactory                  $collectionFactory   [description]
     * @param array                                                        $components          [description]
     * @param array                                                        $data                [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        Usersubscriber $userSubscriberModel,
        \Magento\Newsletter\Model\SubscriberFactory $collectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->groupRepository = $groupRepository;
        $this->userSubscriberModel = $userSubscriberModel;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $collModel = $this->userSubscriberModel->getCollection()->addFieldToFilter('group_id', $item['id']);
                $subIds = [];
                foreach ($collModel as $key => $value) {
                    $subIds[] = $value->getSubId();
                }
                $collection = $this->collectionFactory->create()->getCollection()->addFieldToSelect('subscriber_id')->addFieldToFilter('subscriber_id', ['in' => $subIds]);
                $totalSub = count($collection->getData());
                $data = explode(',', $item['customer_groups']);
                $groupName = [];
                foreach ($data as $key => $value) {
                    if ($value != '') {
                        $group = $this->groupRepository->getById($value);
                        $groupName[] = $group->getCode();
                    }
                }
                $item['customer_groups'] = $totalSub;
                if (!empty($groupName)) {
                    $item['customer_groups'] .= "<div style='background: #D5ECCA;padding: 5px 10px;border: 1px solid #97DB78;'><h4>Assigned to user groups:</h4><p>" . implode(',', $groupName) . "<br></p></div>";
                }
            }
        }
        return $dataSource;
    }
}
