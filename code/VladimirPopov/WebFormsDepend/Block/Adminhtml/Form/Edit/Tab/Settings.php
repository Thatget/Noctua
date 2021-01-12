<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [30-04-2019]
 */

namespace VladimirPopov\WebFormsDepend\Block\Adminhtml\Form\Edit\Tab;

class Settings extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \VladimirPopov\WebForms\Model\Config\Captcha
     */
    protected $_captchaConfig;

    /**
     * @var Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $productAttributeRepository;

    /**
     * \VladimirPopov\WebForms\Model\FieldFactory
     */
    protected $_fieldFactory;

    /**
     * @var \Emipro\Newslettergroup\Model\Newsletter
     */
    protected $newsletterData;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context             $context                    [description]
     * @param \Magento\Framework\Registry                         $registry                   [description]
     * @param \Magento\Framework\Data\FormFactory                 $formFactory                [description]
     * @param \Magento\Store\Model\System\Store                   $systemStore                [description]
     * @param \VladimirPopov\WebForms\Model\Config\Captcha        $captchaConfig              [description]
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository [description]
     * @param \VladimirPopov\WebForms\Model\FieldFactory          $fieldFactory               [description]
     * @param \Emipro\Newslettergroup\Model\Newsletter            $newsletterData             [description]
     * @param array                                               $data                       [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \VladimirPopov\WebForms\Model\Config\Captcha $captchaConfig,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \VladimirPopov\WebForms\Model\FieldFactory $fieldFactory,
        \Emipro\Newslettergroup\Model\Newsletter $newsletterData,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_captchaConfig = $captchaConfig;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->_fieldFactory = $fieldFactory;
        $this->newsletterData = $newsletterData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Form */
        $model = $this->_coreRegistry->registry('webforms_form');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Vladimipopov_WebForms::form_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'VladimirPopov\WebForms\Block\Adminhtml\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element_renderer'
            )
        );
        $form->setDataObject($model);

        $form->setHtmlIdPrefix('form_');
        $form->setFieldNameSuffix('form');

        $fieldset = $form->addFieldset('webforms_general', array(
            'legend' => __('General Settings'),
        ));

        $fieldset->addField('accept_url_parameters', 'select', array(
            'label' => __('Accept URL parameters'),
            'title' => __('Accept URL parameters'),
            'name' => 'accept_url_parameters',
            'required' => false,
            'note' => __('Accept URL parameters to set field values. Use field Code value as parameter name'),
            'options' => ['1' => __('Yes'), '0' => __('No')],
        ));

        $fieldset->addField('survey', 'select', array(
            'label' => __('Survey mode'),
            'title' => __('Survey mode'),
            'name' => 'survey',
            'required' => false,
            'note' => __('Survey mode allows filling up the form only one time'),
            'options' => ['1' => __('Yes'), '0' => __('No')],
        ));

        $fieldset->addField('redirect_url', 'text', array(
            'label' => __('Redirect URL'),
            'title' => __('Redirect URL'),
            'name' => 'redirect_url',
            'note' => __('Redirect to specified url after successful submission'),
        ));
        /*Changed by MD for Soap and Use Express Delivery Settings[START][30-04-2019]*/
        $fieldset->addField('use_in_soap', 'select', array(
            'label' => __('Use In Soap'),
            'title' => __('Use In Soap'),
            'name' => 'use_in_soap',
            'required' => false,
            'note' => __('Use in soap set =>yes if you want to get in soap api'),
            'options' => ['1' => __('Yes'), '0' => __('No')],
        ));
        $fieldset->addField('use_express_delivery', 'select', array(
            'label' => __('Use Express Delivery'),
            'title' => __('Use Express Delivery'),
            'name' => 'use_express_delivery',
            'required' => false,
            'note' => __('Use express delivery set =>yes if you want to display express delivery'),
            'options' => ['1' => __('Yes'), '0' => __('No')],
        ));
        /*Changed by MD for Soap and Use Express Delivery Settings[END][30-04-2019]*/

        $fieldset = $form->addFieldset('webforms_approval', array(
            'legend' => __('Result Approval Settings'),
        ));

        $approve = $fieldset->addField('approve', 'select', array(
            'label' => __('Enable result approval controls'),
            'title' => __('Enable result approval controls'),
            'name' => 'approve',
            'required' => false,
            'note' => __('You can switch submission result status: pending, approved or not approved'),
            'options' => ['1' => __('Yes'), '0' => __('No')],
        ));

        $email_result_approval = $fieldset->addField('email_result_approval', 'select', array(
            'label' => __('Enable approval status notification'),
            'title' => __('Enable approval status notification'),
            'name' => 'email_result_approval',
            'required' => false,
            'note' => __('Send customer notification email on submission result status change'),
            'options' => ['1' => __('Yes'), '0' => __('No')],
        ));

        $bcc_approval_email = $fieldset->addField('bcc_approval_email', 'text', array(
            'label' => __('Bcc e-mail address'),
            'note' => __('Send blind carbon copy of notification to specified address. You can set multiple addresses comma-separated'),
            'name' => 'bcc_approval_email',
        ));

        $email_result_notapproved_template = $fieldset->addField('email_result_notapproved_template_id', 'select', array(
            'label' => __('Result NOT approved notification email template'),
            'title' => __('Result NOT approved notification email template'),
            'name' => 'email_result_notapproved_template_id',
            'required' => false,
            'values' => $model->getTemplateOptions(),
        ));

        $email_result_approved_template = $fieldset->addField('email_result_approved_template_id', 'select', array(
            'label' => __('Result approved notification email template'),
            'title' => __('Result approved notification email template'),
            'name' => 'email_result_approved_template_id',
            'required' => false,
            'values' => $model->getTemplateOptions(),
        ));

        $email_result_completed_template = $fieldset->addField('email_result_completed_template_id', 'select', array(
            'label' => __('Result completed notification email template'),
            'title' => __('Result completed notification email template'),
            'name' => 'email_result_completed_template_id',
            'required' => false,
            'values' => $model->getTemplateOptions(),
        ));

        $fieldset = $form->addFieldset('webforms_captcha', array(
            'legend' => __('reCaptcha Settings'),
        ));

        $fieldset->addField('captcha_mode', 'select', array(
            'label' => __('Captcha mode'),
            'title' => __('Captcha mode'),
            'name' => 'captcha_mode',
            'required' => false,
            'note' => __('Default value is set in Forms Settings'),
            'values' => $this->_captchaConfig->toOptionArray(true),
        ));

        $fieldset = $form->addFieldset('webforms_files', array(
            'legend' => __('Files Settings'),
        ));

        $fieldset->addField('files_upload_limit', 'text', array(
            'label' => __('Files upload limit'),
            'title' => __('Files upload limit'),
            'name' => 'files_upload_limit',
            'class' => 'validate-number',
            'note' => __('Maximum upload file size in kB'),
        ));

        $fieldset = $form->addFieldset('webforms_images', array(
            'legend' => __('Images Settings'),
        ));

        $fieldset->addField('images_upload_limit', 'text', array(
            'label' => __('Images upload limit'),
            'title' => __('Images upload limit'),
            'class' => 'validate-number',
            'name' => 'images_upload_limit',
            'note' => __('Maximum upload image size in kB'),
        ));
        /*Changed by MD for Newsletter Settings[START][30-04-2019]*/
        if ($this->showNewsletterConfig()) {

            $fieldset = $form->addFieldset('webforms_newsletter', array(
                'legend' => __('Newsletter Settings'),
            ));

            $fieldset->addField('source_website', 'select', array(
                'label' => __('Subscriber Source'),
                'title' => __('Subscriber Source'),
                'name' => 'source_website',
                'required' => false,
                'note' => __('Default Subscriber Source'),
                'values' => $this->getSubscriberSource(),
            ));

            $fieldset->addField('source_group', 'select', array(
                'label' => __('Subscriber Group'),
                'title' => __('Subscriber Group'),
                'name' => 'source_group',
                'required' => false,
                'note' => __('Default Subscriber Group'),
                'values' => $this->getNewsletterGroups(),
            ));
        }
        /*Changed by MD for Newsletter Settings[END][30-04-2019]*/

        $this->_eventManager->dispatch('adminhtml_webforms_form_edit_tab_settings_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /*Changed by MD for Newsletter Settings[START][30-04-2019]*/
    //Check subscribe type field exist or not
    public function showNewsletterConfig()
    {
        $param = $this->getRequest()->getParam("id", 0);
        if ($param == 0) {
            return false;
        } else {
            $fields = $this->_fieldFactory->create()
                ->setStoreId($this->getStoreId())
                ->getCollection()
                ->addFieldToSelect("type")
                ->addFilter('webform_id', $param)
                ->addFilter("type", "subscribe");
            $check = $fields->getFirstItem()->getData();
            if (isset($check["type"]) && $check["type"] == "subscribe") {
                return true;
            } else {
                return false;
            }
        }
    }
    //Get Subscriber Source
    public function getSubscriberSource()
    {
        $options = $this->productAttributeRepository->get('newsletter_website')->getOptions();
        $subscriberSource = [];
        foreach ($options as $option) {
            $subscriberSource[$option->getValue()] = $option->getLabel();
        }
        return $subscriberSource;
    }
    //Get Newsletter Groups
    public function getNewsletterGroups()
    {
        $newsletterData = $this->newsletterData->getCollection();
        $gropuTitle[0] = ' ';
        foreach ($newsletterData as $key => $value) {
            $gropuTitle[$value['id']] = $value['title'];
        }
        return $gropuTitle;
    }
    /*Changed by MD for Newsletter Settings[END][30-04-2019]*/
}
