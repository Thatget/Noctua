<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [11-07-2019]
 */

namespace Emipro\Newslettergroup\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Subscriber extends \Magento\Newsletter\Model\Subscriber
{

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Newsletter\Helper\Data $newsletterData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime = null,
        CustomerInterfaceFactory $customerFactory = null,
        DataObjectHelper $dataObjectHelper = null
    ) {
        parent::__construct($context, $registry, $newsletterData, $scopeConfig, $transportBuilder, $storeManager, $customerSession, $customerRepository, $customerAccountManagement, $inlineTranslation, $resource, $resourceCollection, $data, $dateTime, $customerFactory, $dataObjectHelper);
        $this->request = $request;
    }
    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws \Exception
     * @return int
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function subscribe($email)
    {
        $this->loadByEmail($email);

        if ($this->getId() && $this->getStatus() == self::STATUS_SUBSCRIBED) {
            return $this->getStatus();
        }

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = $this->_scopeConfig->getValue(
            self::XML_PATH_CONFIRMATION_FLAG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == 1 ? true : false;

        $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn()
        && $this->_customerSession->getCustomerDataObject()->getEmail() == $email;

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                $this->setStatus(self::STATUS_NOT_ACTIVE);
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
        }

        if ($isSubscribeOwnEmail) {
            try {
                $customer = $this->customerRepository->getById($this->_customerSession->getCustomerId());
                $this->setStoreId($customer->getStoreId());
                $this->setCustomerId($customer->getId());
            } catch (NoSuchEntityException $e) {
                $this->setStoreId($this->_storeManager->getStore()->getId());
                $this->setCustomerId(0);
            }
        } else {
            $this->setStoreId($this->_storeManager->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setStatusChanged(true);
        /*Changed by MD for set source web-site & subscription date[START][11-07-2019]*/
        $sourceWebsite = $this->request->getParam('source_website');
        if ($sourceWebsite) {
            $this->setSourceWebsite($sourceWebsite);
        }
        $this->setSubscriptionDate(date("d-m-Y H:i:s"));
        /*Changed by MD for set source web-site & subscription date[END][11-07-2019]*/

        try {
            /* Save model before sending out email */
            $this->save();
            /*Changed by MD for sent confirmation email or not[START][11-07-2019]*/
            $notifyEmail = $this->request->getParam('notify_email');
            if ($notifyEmail == 1) {
                if ($isConfirmNeed === true) {
                    $this->sendConfirmationRequestEmail();
                } else {
                    $this->sendConfirmationSuccessEmail();
                }
            }
            /*Changed by MD for sent confirmation email or not[END][11-07-2019]*/
            return $this->getStatus();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /*Changed by MD for send Newsletter Success email only to Not Activated users[START][11-07-2019]*/
    public function confirmSub($code, $status = null)
    {
        if ($this->getCode() == $code) {
            $this->setStatus(self::STATUS_SUBSCRIBED)
                ->setStatusChanged(true)
                ->save();
            $notifyEmail = $this->request->getParam('notify_email');
            if ($status == 2 && $notifyEmail == 1) {
                $this->sendConfirmationSuccessEmail();
            }
            return true;
        }
        return false;
    }
    /*Changed by MD for send Newsletter Success email only to Not Activated users[END][11-07-2019]*/
}
