<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Controller\Adminhtml\Newsletter;

use Emipro\Newslettergroup\Model\Newsletter;
use Emipro\Newslettergroup\Model\Usersubscriber;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Newsletter\Model\SubscriberFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Newsletter
     */
    protected $newsLetterGroupModel;

    /**
     * @var Usersubscriber
     */
    protected $userSubscriberModel;

    /**
     * @var Session
     */
    protected $adminsession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * [__construct description]
     * @param Action\Context                              $context              [description]
     * @param Newsletter                                  $newsLetterGroupModel [description]
     * @param Usersubscriber                              $userSubscriberModel  [description]
     * @param \Magento\Framework\Json\EncoderInterface    $jsonEncoder          [description]
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date                 [description]
     * @param \Magento\Framework\App\ResourceConnection   $resourceConnection   [description]
     * @param Session                                     $adminsession         [description]
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager         [description]
     * @param SubscriberFactory|null                      $subscriberFactory    [description]
     */
    public function __construct(
        Action\Context $context,
        Newsletter $newsLetterGroupModel,
        Usersubscriber $userSubscriberModel,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        Session $adminsession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SubscriberFactory $subscriberFactory = null
    ) {
        parent::__construct($context);
        $this->newsLetterGroupModel = $newsLetterGroupModel;
        $this->userSubscriberModel = $userSubscriberModel;
        $this->jsonEncoder = $jsonEncoder;
        $this->date = $date;
        $this->resourceConnection = $resourceConnection;
        $this->adminsession = $adminsession;
        $this->storeManager = $storeManager;
        $this->subscriberFactory = $subscriberFactory ?: ObjectManager::getInstance()->get(SubscriberFactory::class);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            if ($this->getRequest()->getParam('id')) {
                $id = $this->getRequest()->getParam('id');
                $this->newsLetterGroupModel->load($id);
            }
            $data = $this->setCustomerGroup($data);
            try {
                $this->newsLetterGroupModel->setData($data);
                $model = $this->newsLetterGroupModel->save();

                $id = $this->getRequest()->getParam('id');

                if (array_key_exists("subscriber_user", $data)) {
                    $subscriberUserData = json_decode($data['subscriber_user']);
                    $userSubscriberIds = [];
                    foreach ($subscriberUserData as $key => $value) {
                        if ($key != 0) {
                            $userSubscriberIds[] = $key;
                        }
                    }
                    if ($id) {
                        $resource = $this->resourceConnection;
                        $writeConnection = $resource->getConnection('default');
                        $table = $resource->getTableName('emipro_newsletter_user_subscriber');
                        $condition = array($writeConnection->quoteInto('group_id = ?', $id));
                        $writeConnection->DELETE($table, $condition);
                    }
                    /*Changed by MD for Add Subscribers emails [START][11-07-2019]*/
                    if (isset($data['emails'])) {
                        $userSubscriberIds = array_merge($userSubscriberIds, $this->convertEmailsToSubscribers($data['emails'], $data["notify_email"], $data['store_view'], $data["source_website"]));
                    }
                    /*Changed by MD for Add Subscribers emails [END][11-07-2019]*/
                    $userSubscriberIds = array_unique($userSubscriberIds);
                    foreach ($userSubscriberIds as $value) {
                        $id = $this->getRequest()->getParam('id');

                        if (!$id) {
                            $groupData = $this->newsLetterGroupModel->getCollection();
                            $id = null;
                            foreach ($groupData as $key => $valueGroup) {
                                $id = $valueGroup->getId();
                            }
                        }
                        $this->userSubscriberModel->setData('group_id', $id);
                        $this->userSubscriberModel->setData('sub_id', $value);
                        $this->userSubscriberModel->save();
                        $this->userSubscriberModel->unsetData();
                    }
                } else {
                    /*Changed by MD for Add Subscribers emails [START][11-07-2019]*/
                    if (isset($data['emails'])) {
                        $subscribers = $this->convertEmailsToSubscribers($data['emails'], $data["notify_email"], $data['store_view'], $data["source_website"]);
                        $this->saveSubscribers($subscribers, $model->getId());
                    }
                    /*Changed by MD for Add Subscribers emails [END][11-07-2019]*/
                }
                $this->messageManager->addSuccess(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->newsLetterGroupModel->getId(), '_current' => true]);
                }

                $Usersubscriberdata = $this->userSubscriberModel->load($id);
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.' . $e));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function setCustomerGroup(array $rawData)
    {
        $data = $rawData;
        if (isset($data['customer_groups']) && $data['customer_groups'] != '') {
            $data['customer_groups'] = implode($data['customer_groups'], ',');
        }
        return $data;
    }
    /*Changed by MD for Add Subscribers emails [START][11-07-2019]*/
    public function convertEmailsToSubscribers($emailsString, $notify = 0, $storeId = 0, $sourceWebsite)
    {
        // Get emails from test fields
        $emails = nl2br($emailsString);
        $newEmString = [];
        if (isset($emails) && $emails != "") {
            $mails = explode('<br />', $emails);
            foreach ($mails as $mail) {
                try {
                    if (!\Zend_Validate::is($mail, 'EmailAddress')) {

                    }
                    if ($mail && $mail != "") {
                        if ($storeId == 0) {
                            $storeId = 2;
                        }
                        $this->storeManager->setCurrentStore($storeId);
                        $status = $this->subscriberFactory->create()->subscribe(trim($mail));
                        $user = $this->subscriberFactory->create()->loadByEmail(trim($mail));
                        $user->setSourceWebsite($sourceWebsite);
                        $user->save();
                        if ($status > 0) {
                            $user = $this->subscriberFactory->create()->loadByEmail(trim($mail));
                            $id = $user->getId();
                            $user->confirmSub($user->getCode(), $status);
                            $newEmString[] = $id;
                        }
                    }
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }
        return $newEmString;
    }

    public function saveSubscribers($subscribers, $groupId)
    {
        foreach ($subscribers as $subscriber) {
            $resource = $this->resourceConnection;
            $writeConnection = $resource->getConnection('default');
            $table = $resource->getTableName('emipro_newsletter_user_subscriber');
            $where = ['group_id = ?' => $groupId, 'sub_id = ?' => $subscriber];
            $writeConnection->DELETE($table, $where);

            $this->userSubscriberModel->setData('group_id', $groupId);
            $this->userSubscriberModel->setData('sub_id', $subscriber);
            $this->userSubscriberModel->save();
            $this->userSubscriberModel->unsetData();
        }
        return true;
    }
    /*Changed by MD for Add Subscribers emails [END][11-07-2019]*/
}
