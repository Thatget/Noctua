<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [02-05-2019]
 */

namespace VladimirPopov\WebFormsDepend\Block;

use Magento\Framework\View\Element\Template;

class Form extends \Magento\Framework\View\Element\Template
{
    protected $_form;

    protected $_filterProvider;

    protected $_coreRegistry;

    protected $_formFactory;

    protected $fieldCollectionFactory;

    protected $_customerSession;

    protected $_customerSessionFactory;

    protected $_success;

    protected $_customerUrl;

    protected $_resultCollectionFactory;

    protected $_resultFactory;

    protected $_response;

    protected $_captcha;

    protected $_storeManager;

    protected $_uid;

    protected $random;

    protected $webformsHelper;

    protected $_fieldFactory;

    protected $_urlInterface;

    protected $scopeConfig;

    protected $resource;

    /**
     * [__construct description]
     * @param \Magento\Customer\Model\SessionFactory                               $customerSessionFactory  [description]
     * @param Template\Context                                                     $context                 [description]
     * @param \Magento\Cms\Model\Template\FilterProvider                           $filterProvider          [description]
     * @param \Magento\Framework\Registry                                          $coreRegistry            [description]
     * @param \VladimirPopov\WebForms\Model\FormFactory                            $formFactory             [description]
     * @param \Magento\Customer\Model\Url                                          $customerUrl             [description]
     * @param \VladimirPopov\WebForms\Model\ResourceModel\Result\CollectionFactory $resultCollectionFactory [description]
     * @param \VladimirPopov\WebForms\Model\ResultFactory                          $resultFactory           [description]
     * @param \Magento\Framework\App\Response\Http                                 $response                [description]
     * @param \VladimirPopov\WebForms\Model\CaptchaFactory                         $captcha                 [description]
     * @param \Magento\Store\Model\StoreManager                                    $storeManager            [description]
     * @param \Magento\Framework\Math\Random                                       $random                  [description]
     * @param \VladimirPopov\WebForms\Helper\Data                                  $webformsHelper          [description]
     * @param \VladimirPopov\WebForms\Model\ResourceModel\Field\CollectionFactory  $fieldCollectionFactory  [description]
     * @param \VladimirPopov\WebForms\Model\FieldFactory                           $fieldFactory            [description]
     * @param \Magento\Framework\UrlInterface                                      $urlInterface            [description]
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                   $scopeConfig             [description]
     * @param \Magento\Framework\App\ResourceConnection                            $resource                [description]
     * @param array                                                                $data                    [description]
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Registry $coreRegistry,
        \VladimirPopov\WebForms\Model\FormFactory $formFactory,
        \Magento\Customer\Model\Url $customerUrl,
        \VladimirPopov\WebForms\Model\ResourceModel\Result\CollectionFactory $resultCollectionFactory,
        \VladimirPopov\WebForms\Model\ResultFactory $resultFactory,
        \Magento\Framework\App\Response\Http $response,
        \VladimirPopov\WebForms\Model\CaptchaFactory $captcha,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Math\Random $random,
        \VladimirPopov\WebForms\Helper\Data $webformsHelper,
        \VladimirPopov\WebForms\Model\ResourceModel\Field\CollectionFactory $fieldCollectionFactory,
        \VladimirPopov\WebForms\Model\FieldFactory $fieldFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_customerUrl = $customerUrl;
        $this->_filterProvider = $filterProvider;
        $this->_coreRegistry = $coreRegistry;
        $this->_formFactory = $formFactory;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_resultCollectionFactory = $resultCollectionFactory;
        $this->_resultFactory = $resultFactory;
        $this->_response = $response;
        $this->_captcha = $captcha;
        $this->_storeManager = $storeManager;
        $this->random = $random;
        $this->webformsHelper = $webformsHelper;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->_fieldFactory = $fieldFactory;
        $this->_urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }

    /*Changed by MD for getResultId, It's call from default.phtml[START][01-05-2019]*/
    public function getResultId($post_form_id)
    {
        $data = $this->_resultFactory->create()->load($post_form_id);
        return $data->getId();
    }
    public function getFieldsData($form_id, $post_form_id)
    {
        $fieldsData = $this->_fieldFactory->create()
            ->getCollection()
            ->addFieldToFilter('webform_id', $form_id)
            ->addFieldToSelect(['id', 'name', 'code']);
        $fieldsData->getSelect()->join(['webformResult' => "webforms_results_values"], 'main_table.id = webformResult.field_id', ['*'])->where('result_id =' . $post_form_id);

        return $fieldsData;
    }
    public function getFieldsConf($webform_id)
    {
        $fieldsData = $this->_fieldFactory->create()
            ->getCollection()
            ->addFieldToFilter('webform_id', $webform_id);
        return $fieldsData;
    }
    public function getItemNumber($item_number)
    {
        $data = $this->_resultFactory->create()->load($item_number);
        return $data;
    }
    public function getWebformData($webform_id)
    {
        $data = $this->_formFactory->create()->load($webform_id);
        return $data;
    }
    public function currentUrl()
    {
        return $this->_urlInterface->getCurrentUrl();
    }
    public function paymentModeConfig()
    {
        return $this->scopeConfig->getValue('webforms/expressdelivery/paypal_paymentmode', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
    public function paypalEmailConfig()
    {
        return $this->scopeConfig->getValue('webforms/expressdelivery/emailpaypal', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
    public function resourceConnect()
    {
        return $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
    }
    /*Changed by MD for getResultId, It's call from default.phtml[END][01-05-2019]*/

    /**
     * @return \VladimirPopov\WebForms\Model\Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }

    public function setSuccess($value)
    {
        $this->_success = $value;
    }

    public function getSuccess()
    {
        return $this->_success;
    }

    public function getScope()
    {
        return \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    }

    public function getRegistry()
    {
        return $this->_coreRegistry;
    }

    public function getSession()
    {
        return $this->_customerSession;
    }

    protected function _toHtml()
    {
        if ($this->_coreRegistry->registry('webforms_preview')) {
            if (strlen($this->_storeManager->getStore()->getConfig('webforms/general/preview_template')) > 1) {
                /*Changed by MD for set default phtml file[START][07-06-2019]*/
                $this->setTemplate('VladimirPopov_WebFormsDepend::webforms/form/default.phtml');
                /*Changed by MD for set default phtml file[END][07-06-2019]*/
            }
        }

        if (!$this->_coreRegistry->registry('webforms_preview')) {
            $this->initForm();
        }

        if (!$this->getForm()->canAccess()) {
            $this->setTemplate('VladimirPopov_WebForms::webforms/access_denied.phtml');
        }

        $html = parent::_toHtml();

        $messages = $this->getLayout()->getMessagesBlock();
        $production = $this->webformsHelper->isProduction();
        if (!$production['verified']) {
            $messages->addNotice($this->webformsHelper->getNote());
        }

        $html = $messages->getGroupedHtml() . $html;

        return $html;
    }

    public function getPageFilter()
    {
        return $this->_filterProvider->getPageFilter();
    }

    public function getResultFromUrl()
    {
        $result = $this->_resultFactory->create()->setId(true);
        $data = [];
        if ($this->_form) {
            if ($this->_form->getData('accept_url_parameters')) {
                $urlParams = $this->getRequest()->getParams();
                foreach ($urlParams as $fieldCode => $value) {
                    $field = $this->fieldCollectionFactory->create()->addFilter('webform_id', $this->_form->getId())->addFilter('code', $fieldCode)->getFirstItem();
                    if ($field->getId()) {
                        $data[$field->getId()] = $value;
                        $result->setData('field_' . $field->getId(), $value);
                    }
                }
            }
        }

        $result->setData('field', $data);

        return $result;
    }

    protected function initForm()
    {

        $show_success = false;
        $data = $this->getFormData();

        $form = $this->_formFactory->create()
            ->setStoreId($this->_storeManager->getStore()->getId());

        if ($this->getData('webform_code')) {
            $form = $form->getCollection()
                ->addFilter('code', $this->getData('webform_code'))
                ->getFirstItem();
        }

        if (!$form->getId()) {
            $form->load($data['webform_id']);
        }

        $this->setForm($form);

        $form->getFieldsToFieldsets(false, $this->getResultFromUrl());
        $this->_coreRegistry->unregister('webform');
        $this->_coreRegistry->register('webform', $form);

        //delete form temporary data
        if ($this->isAjax()) {
            $this->_session->setData('webform_result_tmp_' . $form->getId(), false);
        }

        //proccess texts

        if ($form->getDescription()) {
            $this->setDescription($this->getPageFilter()->filter($form->getDescription()));
        }

        if ($form->getSuccessText()) {
            $this->setSuccessText($this->getPageFilter()->filter($form->getSuccessText()));
        }

        $this->_customerSession = $this->_customerSessionFactory->create();
        $loggedIn = $this->_customerSession->isLoggedIn();
        if ($form->getSurvey()) {
            $collection = $this->_resultCollectionFactory->create()->addFilter('webform_id', $data['webform_id']);

            if ($loggedIn) {
                $collection->addFilter('customer_id', $this->_customerSession->getCustomerId());
            } else {
                $customerSession_validator = $this->_customerSession->getData('_customerSession_validator_data');
                $collection->addFilter('customer_ip', ip2long($customerSession_validator['remote_addr']));
            }
            $count = $collection->count();

            if ($count > 0) {
                $show_success = true;
            }
        }

        if ($this->_customerSession->getFormSuccess() == $this->getForm()->getId() || $show_success) {
            $this->setSuccess(true);
            $this->_customerSession->setFormSuccess();
            if ($this->_customerSession->getData('webform_result_' . $form->getId())) {

                // apply custom variables
                $filter = $this->getPageFilter();
                $formObject = new \Magento\Framework\DataObject;
                $formObject->setData($form->getData());
                $resultObject = $this->_resultFactory->create()->load(($this->_customerSession->getData('webform_result_' . $form->getId())));
                $subject = $resultObject->getEmailSubject('customer');
                $filter->setVariables(array(
                    'webform_result' => $resultObject->toHtml('customer'),
                    'result' => $resultObject->getTemplateResultVar(),
                    'webform' => $formObject,
                    'webform_subject' => $subject,
                ));
            }
        }
        if ($form->getAccessEnable() && !$loggedIn && !$this->getData('results')) {
            $this->_customerSession->setBeforeAuthUrl($this->_urlBuilder->getCurrentUrl());
            $login_url = $this->_customerUrl->getLoginUrl();
            $status = 301;

            if ($this->_storeManager->getStore()->getConfig('webforms/general/login_redirect')) {
                $login_url = $this->getUrl($this->_storeManager->getStore()->getConfig('webforms/general/login_redirect'));

                if (strstr($this->_storeManager->getStore()->getConfig('webforms/general/login_redirect'), '://')) {
                    $login_url = $this->_storeManager->getStore()->getConfig('webforms/general/login_redirect');
                }

            }
            return $this->_response->setRedirect($login_url, $status);
        }
        // switch to async template
        if ($this->getData('async_load')) {
            $this->setData('widget_template', $this->getTemplate());
            $this->setData('async_url', $this->getUrl('webforms/form/load', ['key' => '#WIDGET_KEY#', '_current' => true]));
            $this->setTemplate('webforms/form/async.phtml');
        }

        return $this;
    }

    // check that form is available for direct access
    public function isDirectAvailable()
    {
        $available = new \Magento\Framework\DataObject;
        $status = true;
        if ($this->_coreRegistry->registry('webforms_preview') && !$this->_storeManager->getStore()->getConfig('webforms/general/preview_enabled')) {
            $status = false;
        }

        $available->setData('status', $status);

        $this->_eventManager->dispatch('webforms_direct_available', array
            (
                'available' => $available,
                'form_data' => $this->getFormData(),
            ));

        return $available->getData('status');
    }

    public function getNotAvailableMessage()
    {
        $message = __('Web-form is not active.');

        if ($this->getForm()->getIsActive() && !$this->isDirectAvailable()) {
            $message = __('Web-form is locked by configuration and can not be accessed directly.');
        }

        return $message;
    }

    public function getFormData()
    {
        $data = $this->getRequest()->getParams();

        if (isset($data['id'])) {
            $data['webform_id'] = $data['id'];
        }

        if ($this->getData('webform_id')) {
            $data['webform_id'] = $this->getData('webform_id');
        }

        if (empty($data['webform_id'])) {
            $data['webform_id'] = $this->_storeManager->getStore()->getConfig('webforms/contacts/webform');
        }
        return $data;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->_coreRegistry->registry('webforms_preview')) {

            $this->initForm();
            $this->pageConfig->getTitle()->set($this->getForm()->getName());
        }
    }

    public function getCaptcha()
    {
        return $this->getForm()->getCaptcha();
    }

    public function getEnctype()
    {
        return 'multipart/form-data';
    }

    public function isAjax()
    {
        return $this->_storeManager->getStore()->getConfig('webforms/general/ajax');
    }

    public function getFormAction()
    {
        return $this->getUrl('webforms/form/submit', ['_secure' => true, 'ajax' => $this->isAjax()]);
    }

    public function honeypot()
    {
        return $this->_storeManager->getStore()->getConfig('webforms/honeypot/enable');
    }

    public function getUid()
    {
        if (!$this->_uid) {
            $this->_uid = $this->random->getRandomString(6);
        }
        return $this->_uid;
    }

    public function getFieldUid($field_id)
    {
        return $this->getUid() . $field_id;
    }

    public function getSubmitVisibility()
    {
        $_targets = $this->getForm()->_getLogicTarget();
        foreach ($_targets as $target) {
            if ($target['id'] == 'submit') {
                return $target['logic_visibility'];
            }

        }
        return 'visible';
    }

    public function getFormKey()
    {
        return $this->getData('form_key');
    }
}
