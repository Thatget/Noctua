<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qsoft\WebFormsCustom\Plugin\VladimirPopov\WebForms\Model;

/**
 * Class Form
 *
 * @package Qsoft\WebFormsCustom\Plugin\VladimirPopov\WebForms\Model
 */
class Form extends \VladimirPopov\WebForms\Model\Form
{
    /**
     * Replace @ to a in firstname, lastname
     *
     * @param $field
     * @param $postData
     */
    public function processName($field, $postData)
    {
        if (in_array($field->getCode(), ['FirstName', 'LastName'])) {
            $postData['field'][$field->getId()] = str_replace(
                "@",
                "a",
                $postData['field'][$field->getId()]
            );
        }
    }

    /**
     * Replace @ to a in firstname, lastname
     *
     * @param \VladimirPopov\WebForms\Model\Form $subject
     * @param callable $proceed
     * @param array $config
     * @return false|\VladimirPopov\WebForms\Model\Result
     * @throws \Exception
     */
    public function aroundSavePostResult(
        \VladimirPopov\WebForms\Model\Form $subject,
        callable $proceed,
        $config = []
    ) {
        try {
            $postData = $this->getPost($config);

            $result = $this->_resultFactory->create();

            $new_result = true;
            if (!empty($postData['result_id'])) {
                $new_result = false;
                $result->load($postData['result_id'])->addFieldArray(false, ['select/radio', 'select/checkbox']);

                foreach ($result->getData('field') as $key => $value) {
                    if (!array_key_exists($key, $postData['field'])) {
                        $postData['field'][$key] = '';
                    }
                }

            }

            if (empty($postData['field'])) {
                $postData['field'] = [];
            }

            $this->setData('post_data', $postData);

            $errors = $this->validatePostResult();

            if (count($errors)) {
                foreach ($errors as $error) {
                    $this->messageManager->addError($error);
                    if ($this->_scopeConfig->getValue('webforms/general/store_temp_submission_data')) {
                        $this->_session->setData('webform_result_tmp_' . $this->getId(), $postData);
                    }

                }
                return false;
            }

            $this->_session->setData('webform_result_tmp_' . $this->getId(), false);

            $iplong = ip2long($this->getRealIp());

            $files = $this->getUploadedFiles();
            foreach ($files as $field_name => $file) {
                $field_id = str_replace('file_', '', $field_name);
                if ($file['name']) {
                    $postData['field'][$field_id] = $file['name'];
                }

            }

            // delete files

            foreach ($this->_getFieldsToFieldsets() as $fieldset) {
                foreach ($fieldset['fields'] as $field) {
                    if ($field->getType() == 'file' || $field->getType() == 'image') {
                        if (!empty($postData['delete_file_' . $field->getId()])) {
                            $resultFiles = $this->fileCollectionFactory->create()
                                ->addFilter('result_id', $result->getId())
                                ->addFilter('field_id', $field->getId());
                            foreach ($resultFiles as $resultFile) {
                                $resultFile->delete();
                            }
                            $postData['field'][$field->getId()] = '';
                        }
                    }

                    // Replace @ to a in firstname, lastname
                    $this->processName($field, $postData);
                }
            }

            if ($new_result) {
                $approve = 1;
                if ($this->getApprove()) {
                    $approve = 0;
                }

            }

            $result->setData('field', $postData['field'])
                ->setWebformId($this->getId())
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->setCustomerId($this->_session->getCustomerId());
            /*if ($this->_scopeConfig->getValue('webforms/gdpr/collect_customer_ip')) {*/
            $result->setCustomerIp($iplong);
            //}
            if (!empty($approve)) {
                $result->setApproved($approve);
            }

            $result->setWebform($this);
            $result->save();

            // upload files
            $result->getUploader()->upload();

            $this->_eventManager->dispatch('webforms_result_submit', ['result' => $result, 'webform' => $this]);

            // send e-mail

            if ($new_result) {

                $emailSettings = $this->getEmailSettings();

                // send admin notification
                if ($emailSettings['email_enable']) {
                    $result->sendEmail();
                }

                // send customer notification
                if ($this->getDuplicateEmail()) {
                    $result->sendEmail('customer');
                }

                // email contact
                $logic_rules = $this->getLogic();
                $fields_to_fieldsets = $this->_getFieldsToFieldsets();
                foreach ($fields_to_fieldsets as $fieldset_id => $fieldset) /** @var \VladimirPopov\WebForms\Model\Field $field */ {
                    foreach ($fieldset['fields'] as $field) {
                        foreach ($result->getData() as $key => $value) {
                            if ($key == 'field_' . $field->getId() && strlen($value) && $field->getType() == 'select/contact') {
                                $target_field = ["id" => 'field_' . $field->getId(), 'logic_visibility' => $field->getData('logic_visibility')];

                                if ($this->getLogicTargetVisibility($target_field, $logic_rules, $result->getData('field'))) {
                                    $contactInfo = $field->getContactArray($value);
                                    if (strstr($contactInfo['email'], ',')) {
                                        $contactEmails = explode(',', $contactInfo['email']);
                                        foreach ($contactEmails as $cEmail) {
                                            $result->sendEmail('contact', ['name' => $contactInfo['name'], 'email' => $cEmail]);
                                        }
                                    } else {
                                        $result->sendEmail('contact', $contactInfo);
                                    }
                                }
                            }

                            if ($key == 'field_' . $field->getId() && $value && $field->getType() == 'subscribe') {
                                // subscribe to newsletter
                                $customer_email = $result->getCustomerEmail();
                                foreach ($customer_email as $email) {
                                    $this->_subscriberFactory->create()->subscribe($email);
                                }

                            }
                        }
                    }
                }

            }
            $result->resizeImages();

            $this->dropzoneFactory->create()->cleanup();

            if (!$this->_webformsHelper->isAdmin()) {
                if (
                    $this->getData('delete_submissions')
                    || ($this->getData('show_gdpr_agreement_checkbox')
                        && $this->getData('gdpr_agreement_checkbox_do_not_store')
                        && empty($postData['gdpr']))
                ) {
                    $result->delete();
                }
            }

            return $result;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return false;
        }
    }
}
