<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [06-05-2019]
 */

namespace VladimirPopov\WebFormsDepend\Model;

class Form extends \VladimirPopov\WebForms\Model\Form
{
    protected $_fieldsetCollectionFactory;

    protected $_fieldCollectionFactory;

    protected $_logicFactory;

    protected $_resultFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $_subscriberFactory;

    protected $_storeManager;

    protected $_localDate;

    protected $formKey;

    protected $_uploaderFactory;

    protected $_storeCollectionFactory;

    protected $_webformsHelper;

    protected $dropzoneFactory;

    protected $countryFactory;

    protected $subscriber;

    protected $userSubscriberModel;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \VladimirPopov\WebForms\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \VladimirPopov\WebForms\Model\ResourceModel\Fieldset\CollectionFactory $fieldsetCollectionFactory,
        \VladimirPopov\WebForms\Model\ResourceModel\Field\CollectionFactory $fieldCollectionFactory,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $emailTemplateCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \VladimirPopov\WebForms\Model\FieldFactory $fieldFactory,
        \VladimirPopov\WebForms\Model\FieldsetFactory $fieldsetFactory,
        \VladimirPopov\WebForms\Model\LogicFactory $logicFactory,
        \VladimirPopov\WebForms\Model\StoreFactory $storeFactory,
        \VladimirPopov\WebForms\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \VladimirPopov\WebForms\Model\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \VladimirPopov\WebForms\Model\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $localeDate,
        \VladimirPopov\WebForms\Model\Captcha $captcha,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \VladimirPopov\WebForms\Helper\Data $webformsHelper,
        \VladimirPopov\WebForms\Model\DropzoneFactory $dropzoneFactory,
        \VladimirPopov\WebForms\Model\ResourceModel\Form $resource = null,
        \VladimirPopov\WebForms\Model\ResourceModel\Form\Collection $resourceCollection = null,
        array $data = [],
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Emipro\Newslettergroup\Model\Usersubscriber $userSubscriberModel
    ) {
        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_fieldsetCollectionFactory = $fieldsetCollectionFactory;
        $this->_fieldCollectionFactory = $fieldCollectionFactory;
        $this->_emailTemplateCollection = $emailTemplateCollection;
        $this->_session = $sessionFactory->create();
        $this->_scopeConfig = $scopeConfig;
        $this->_fieldFactory = $fieldFactory;
        $this->_fieldsetFactory = $fieldsetFactory;
        $this->_logicFactory = $logicFactory;
        $this->_request = $request;
        $this->_resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_storeManager = $storeManager;
        $this->_formFactory = $formFactory;
        $this->_localDate = $localeDate;
        $this->_captcha = $captcha;
        $this->formKey = $formKey;
        $this->_uploaderFactory = $uploaderFactory;
        $this->fileCollectionFactory = $fileCollectionFactory;
        $this->_webformsHelper = $webformsHelper;
        $this->dropzoneFactory = $dropzoneFactory;
        $this->_countryFactory = $countryFactory;
        $this->_subscriber = $subscriber;
        $this->userSubscriberModel = $userSubscriberModel;

        parent::__construct($context, $registry, $storeCollectionFactory, $fieldsetCollectionFactory, $fieldCollectionFactory, $emailTemplateCollection, $scopeConfig, $sessionFactory, $fieldFactory, $fieldsetFactory, $logicFactory, $storeFactory, $fileCollectionFactory, $request, $resultFactory, $messageManager, $subscriberFactory, $formFactory, $localeDate, $captcha, $storeManager, $formKey, $uploaderFactory, $webformsHelper, $dropzoneFactory, $resource, $resourceCollection);
    }

    public function sendFormMail($result, $fields)
    {
        try {
            $emailSettings = $this->getEmailSettings();
            /**
             * Changes MD for newsletter GDPR changes[START][06-05-2019]
             */
            $result = $this->_resultFactory->create()->load($result->getId());
            /**
             * Changes MD for newsletter GDPR changes[END][06-05-2019]
             */
            // send admin notification
            /*if ($emailSettings['email_enable']) {
            $result->sendEmail();
            }*/

            // send customer notification
            /*if ($this->getDuplicateEmail()) {
            $result->sendEmail('customer');
            }*/

            // email contact
            foreach ($fields as $field) {
                /**
                 * Changes MD for newsletter GDPR changes[START][06-05-2019]
                 */
                if ($field->getCode() == "City") {
                    $state_value = $result['field_' . $field->getId()];
                }
                if ($field->getCode() == "Country") {
                    $country_code = $result['field_' . $field->getId()];
                    $country_collection = $this->_countryFactory->create()->loadByCode($country_code);
                    $country_value = $country_collection->getName();
                }
                if ($field->getCode() == "E-Mail") {
                    $emailId = $result['field_' . $field->getId()];
                }
                foreach ($result->getData() as $key => $value) {
                    if ($key == 'field_' . $field->getId() && $value && $field->getType() == 'select/contact') {
                        $result->sendEmail('contact', $field->getContactArray($value));
                    }

                    if ($key == 'field_' . $field->getId() && $value && $field->getType() == 'subscribe') {
                        /*$customer_email = $result->getCustomerEmail();
                        foreach ($customer_email as $email) {*/
                        //$this->_subscriber->subscribe($email);
                        //}

                        // Subscribe to newsletter
                        //$this->_subscriber->subscribe($emailId);
                        //Assigne subscriber in Newsletter Source and Newsletter Group
                        $this->assignNewsletterSource($emailId, $country_value, $state_value);
                        $this->assignNewsletterGroup($emailId);
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return false;
        }
    }

    public function assignNewsletterSource($email, $country_value, $state_value)
    {
        $subscriberId = $this->_subscriber->loadByEmail($email);
        $subscriberId->setSourceWebsite($this->getData("source_website"));
        $subscriberId->setSubscriberCountry($country_value);
        $subscriberId->setSubscriberState($state_value);
        $subscriberId->setId($subscriberId->getId())->save();
    }
    public function assignNewsletterGroup($email)
    {
        $groupId = $this->getData("source_group");
        if ($email) {
            $subscriberId = $this->_subscriber->loadByEmail($email)->getId();
        }
        if (!$subscriberId) {
            return false;
        }

        $isExists = $this->userSubscriberModel->getCollection()
            ->addFieldToFilter('group_id', $groupId)
            ->addFieldToFilter('sub_id', $subscriberId)
            ->getFirstItem();
        if (!$isExists->getData()) {
            $this->userSubscriberModel->setData(['group_id' => $groupId, 'sub_id' => $subscriberId])->setId(null)->save();
            return true;
        }

        return false;
    }

}
