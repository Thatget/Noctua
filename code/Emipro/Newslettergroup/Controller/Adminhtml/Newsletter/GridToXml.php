<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Controller\Adminhtml\Newsletter;

use Emipro\Newslettergroup\Model\ResourceModel\Newsletter\CollectionFactory;
use Emipro\Newslettergroup\Model\Usersubscriber;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class GridToXml extends Action
{
    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $subscriberCollection;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var CollectionFactory
     */
    protected $newsGroupFactory;

    /**
     * @var Usersubscriber
     */
    protected $userSubscriberModel;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context            $context             [description]
     * @param \Magento\Eav\Model\Config                      $eavConfig           [description]
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository     [description]
     * @param CollectionFactory                              $newsGroupFactory    [description]
     * @param Usersubscriber                                 $userSubscriberModel [description]
     * @param FileFactory                                    $fileFactory         [description]
     * @param CollectionFactory                                    $subscriberCollection         [description]
     */
    public function __construct(
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscriberCollection,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        CollectionFactory $newsGroupFactory,
        Usersubscriber $userSubscriberModel,
        FileFactory $fileFactory
    ) {
        $this->subscriberCollection = $subscriberCollection;
        $this->eavConfig = $eavConfig;
        $this->groupRepository = $groupRepository;
        $this->newsGroupFactory = $newsGroupFactory;
        $this->userSubscriberModel = $userSubscriberModel;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * generate xml file
     *
     * @return xml file
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $this->_view->loadLayout(false);
        $fileName = "Ngroups(" . date('Y-m-d') . ").xml";
        $exportBlock = $this->newsGroupFactory->create();
        $this->_fileFactory = $this->fileFactory;
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'newsletter_website');

        $csvData = '<?xml version="1.0" encoding="UTF-8"?><items>';
        foreach ($exportBlock as $key => $value) {
            $collModel = $this->userSubscriberModel->getCollection()->addFieldToFilter('group_id', $value->getId());
            $data = explode(',', $value->getCustomerGroups());
            $groupName = [];
            foreach ($data as $keyData => $valueData) {
                if ($valueData != '') {
                    $group = $this->groupRepository->getById($valueData);
                    $groupName[] = $group->getCode();
                }
            }

            $customerGroupCount = count($collModel->getData());
            if (!empty($groupName)) {
                $customerGroupCount .= ' (Assigned to user groups: ' . implode(',', $groupName) . ')';
            }

            // custom 31/07/2020
            $emails = [];
            $flag = 0;
            try {
                if (array_search($value->getId(), $params['selected']) !== false){
                    foreach ($collModel as $model) {
                        $subIds[] = $model->getSubId();
                        $subId = $model->getSubId();
                        $subscriber = $this->subscriberCollection->create()
                            ->addFieldToFilter('subscriber_id', ['eq' => $subId])
                            ->getFirstItem();
                        $subEmail = $subscriber->getSubscriberEmail();
                        if ($subEmail) {
                            $emails[] = $subEmail;
                        }
                        $flag = 1;
                    }
                    $email[] = "";
                }
            }catch (\Exception $e){
                $emails = [];
                $flag = 2;
            }

            if ($flag == 2){
                $csvData .= "<item>";
                $csvData .= "<id><![CDATA[" . $value->getId() . "]]></id>";
                $csvData .= "<title><![CDATA[" . $value->getTitle() . "]]></title>";
                $csvData .= "<number_of_users><![CDATA[" . $customerGroupCount . "]]></number_of_users>";
                $csvData .= "<source_website><![CDATA[" . $attribute->getSource()->getOptionText($value->getSourceWebsite()) . "]]></source_website>";
                $csvData .= "<creation_time><![CDATA[" . $value->getCreationTime() . "]]></creation_time>";
                $csvData .= "</item>";
            }elseif ($flag == 1){
                $csvData .= "<item>";
                $csvData .= "<id><![CDATA[" . $value->getId() . "]]></id>";
                $csvData .= "<title><![CDATA[" . $value->getTitle() . "]]></title>";
                $csvData .= "<number_of_users><![CDATA[" . $customerGroupCount . "]]></number_of_users>";
                $csvData .= "<source_website><![CDATA[" . $attribute->getSource()->getOptionText($value->getSourceWebsite()) . "]]></source_website>";
                $csvData .= "<creation_time><![CDATA[" . $value->getCreationTime() . "]]></creation_time>";
                for ($i = 0; $i < count($emails); $i++) {
                    $csvData .= "<subscriber_email><![CDATA[" . $emails[$i] . "]]></subscriber_email>";
                }
                $csvData .= "</item>";
            }
            // end custom
        }
        $csvData .= '</items>';
        return $this->_fileFactory->create(
            $fileName,
            $csvData,
            DirectoryList::VAR_DIR
        );
    }
}