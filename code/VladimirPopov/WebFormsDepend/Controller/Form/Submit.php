<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [03-05-2019]
 */

namespace VladimirPopov\WebFormsDepend\Controller\Form;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\HTTP\Header;
use Magento\Framework\View\Result\PageFactory;

class Submit extends Action
{
    /**
     * \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * \Magento\Framework\Registry
     */
    protected $resultPageFactory;

    /**
     * PageFactory
     */
    protected $resultHttpFactory;

    /**
     * \Magento\Framework\Json\Encoder
     */
    protected $jsonEncoder;

    /**
     * \VladimirPopov\WebForms\Model\FormFactory
     */
    protected $formFactory;

    /**
     * \VladimirPopov\WebForms\Model\ResultFactory
     */
    protected $resultFactory;

    /**
     * \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * messageManager
     */
    protected $messageManager;

    /**
     * \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * SessionFactory
     */
    protected $customerSession;

    /**
     * SessionFactory
     */
    protected $customerSessionFactory;

    /**
     * Header
     */
    protected $header;

    /**
     * \VladimirPopov\WebForms\Model\FieldFactory
     */
    protected $fieldFactory;

    /**
     * [__construct description]
     * @param Context                                     $context                [description]
     * @param \Magento\Framework\Json\Encoder             $jsonEncoder            [description]
     * @param PageFactory                                 $resultPageFactory      [description]
     * @param \Magento\Cms\Model\Template\FilterProvider  $filterProvider         [description]
     * @param \Magento\Framework\Registry                 $coreRegistry           [description]
     * @param \Magento\Framework\App\Response\HttpFactory $resultHttpFactory      [description]
     * @param \VladimirPopov\WebForms\Model\FormFactory   $formFactory            [description]
     * @param \VladimirPopov\WebForms\Model\ResultFactory $resultFactory          [description]
     * @param \Magento\Store\Model\StoreManager           $storeManager           [description]
     * @param SessionFactory                              $customerSessionFactory [description]
     * @param \VladimirPopov\WebForms\Model\FieldFactory  $fieldFactory           [description]
     * @param \Magento\Framework\App\ResourceConnection   $resource               [description]
     * @param Header                                      $header                 [description]
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Encoder $jsonEncoder,
        PageFactory $resultPageFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\HttpFactory $resultHttpFactory,
        \VladimirPopov\WebForms\Model\FormFactory $formFactory,
        \VladimirPopov\WebForms\Model\ResultFactory $resultFactory,
        \Magento\Store\Model\StoreManager $storeManager,
        SessionFactory $customerSessionFactory,
        \VladimirPopov\WebForms\Model\FieldFactory $fieldFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        Header $header
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->resultHttpFactory = $resultHttpFactory;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_formFactory = $formFactory;
        $this->_resultFactory = $resultFactory;
        $this->_filterProvider = $filterProvider;
        $this->messageManager = $context->getMessageManager();
        $this->_storeManager = $storeManager;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_fieldFactory = $fieldFactory;
        $this->_resource = $resource;
        $this->header = $header;
        parent::__construct($context);
    }

    public function execute()
    {
        //Start Add by Philip to check format Date [compliant with VladimirPopov_WebForms][25/12/2020]
        $fieldData = $this->getRequest()->getPost('field');
        $valueCheckFormat = $this->checkDateFormat($fieldData);
        if (!($valueCheckFormat == "TRUE")){
            $result["errors"] = $valueCheckFormat;
            $json = $this->_jsonEncoder->encode($result);
            $resultHttp = $this->resultHttpFactory->create();
            $resultHttp->setNoCacheHeaders();
            $resultHttp->setHeader('Content-Type', 'text/plain', true);
            return $resultHttp->setContent($json);
        }
        //End Add by Philip to check format Date [compliant with VladimirPopov_WebForms][25/12/2020]

        $webform = $this->_formFactory->create()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->load($this->getRequest()->getParam("webform_id"));

        // Ajax submit
        if ($this->getRequest()->getParam('ajax')) {
            $result = ["success" => false, "errors" => []];
            if ($this->getRequest()->getParam('submitWebform_' . $webform->getId()) && $webform->getIsActive()) {
                $post_data_id = $this->getRequest()->getPost('post_data_id');
                $webform_request_data = $this->_resultFactory->create()->load($post_data_id);
                $webform_request_check_id = $webform_request_data->getId();
                $resultObject = false;
                if ($this->getRequest()->getPost('post_data_id') == "") {
                    $resultObject = $webform->savePostResult();
                }
                if ($this->getRequest()->getPost('post_data_id') != "") {
                    $webform_payment_conf = $this->_fieldFactory->create()->getCollection()->addFieldToFilter('webform_id', $_POST['webform_id']);
                    $field_express_delivery = "";
                    $field_trans_id = "";
                    $field_payment = "";
                    $field_delivery_method = "";
                    $field_amount = "";
                    foreach ($webform_payment_conf as $key => $value) {
                        if ($value['code'] == "express_delivery") {
                            $field_express_delivery = $key;
                        }
                        if ($value['code'] == "delivery_method") {
                            $field_delivery_method = $key;
                        }
                        if ($value['code'] == "amount") {
                            $field_amount = $key;
                        }
                        if ($value['code'] == "payment_confirmed") {
                            $field_payment = $key;
                        }
                        if ($value['code'] == "transaction_id") {
                            $field_trans_id = $key;
                        }
                    }

                    $writeConnection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
                    $resultValueTable = $writeConnection->getTableName('webforms_results_values');
                    if ($this->getRequest()->getPost('delivery_form') == "Yes") {
                        $writeConnection->update($resultValueTable, ['value' => $this->getRequest()->getPost('rdo_shipping')], ['result_id = ?' => $post_data_id, 'field_id = ?' => $field_delivery_method]);

                        $writeConnection->update($resultValueTable, ['value' => $this->getRequest()->getPost('delivery_price')], ['result_id = ?' => $post_data_id, 'field_id = ?' => $field_amount]);
                    } else {
                        $writeConnection->update($resultValueTable, ['value' => ''], ['result_id = ?' => $post_data_id, 'field_id = ?' => $field_express_delivery]);

                        $writeConnection->update($resultValueTable, ['value' => ''], ['result_id = ?' => $post_data_id, 'field_id = ?' => $field_delivery_method]);

                        $writeConnection->update($resultValueTable, ['value' => ''], ['result_id = ?' => $post_data_id, 'field_id = ?' => $field_amount]);

                        $writeConnection->update($resultValueTable, ['value' => ''], ['result_id = ?' => $post_data_id, 'field_id = ?' => $field_payment]);

                        $writeConnection->update($resultValueTable, ['value' => ''], ['result_id = ?' => $post_data_id, 'field_id = ?' => $field_trans_id]);
                    }
                }
                $chkExpress = '';
                if ($webform_request_check_id == "") {
                    if ($this->getRequest()->getPost('chk_express') == "on") {
                        $result["success"] = ($resultObject && is_object($resultObject)) ? $resultObject->getId() : $resultObject;
                        $chkExpress = 1;
                    } else {
                        $result["success"] = ($resultObject && is_object($resultObject)) ? $resultObject->getId() : $resultObject;
                        $chkExpress = 0;
                    }
                } else {
                    $result["success"] = $post_data_id;
                    $webform->setPostData($this->getRequest()->getPost('field'));
                    if ($this->getRequest()->getPost('delivery_form') == "Yes") {
                        $chkExpress = 1;
                    } else {
                        $chkExpress = 0;
                    }
                }
                if ($result["success"] && $chkExpress == 1) {
                    $html_data = "";
                    $flg = false;
                    $price = 0;
                    $express_delivery_taken = false;

                    $fiedData = $this->getRequest()->getPost('field');
                    foreach ($fiedData as $key => $value) {
                        $webform_payment_conf = $this->_fieldFactory->create()->getCollection()->addFieldToFilter('webform_id', $webform->getId())->addFieldToFilter('id', $key);
                        $field_transaction_id = "";
                        $field_payment_confirmed = "";
                        foreach ($webform_payment_conf as $key1 => $value1) {
                            if ($value1['code'] == "express_delivery") {
                                $express_delivery_taken = $value;
                                $flg = true;
                            }
                            if ($value1['code'] == "amount") {
                                $price = $value;
                                $flg = true;
                            }
                        }
                    }

                    if ($flg == true && $express_delivery_taken == "Yes") {
                        $paypalURL = "";
                        $paymentMode = $this->getRequest()->getPost('payment_mode');
                        $paypal_email = $this->getRequest()->getPost('paypal_email');
                        if ($paymentMode == 1) {
                            $paypalURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
                            $curr_code = 'USD';
                        } else {
                            $paypalURL = 'https://www.paypal.com/cgi-bin/webscr';
                            $curr_code = 'EUR';
                        }
                        $url = strtok($this->getRequest()->getPost("ret_url"), '?');
                        $encode_url_str = urlencode(base64_encode($webform->getId() . "||" . $result["success"]));
                        $html_data = "<div class='product info detailed'>
                                        <div class='section-inner-presse-page'>
                                             <p id='webform-redirect-paypal' style='font-size:12px;'>" . __('Please wait. You are redirecting to PayPal...') . "</p>
                                        </div>
                                    </div>
                                    <form id='frm_paypal_submit' accept-charset='utf-8' name='frm_paypal_submit' action='" . $paypalURL . "' method='post'>
                                       <input type='hidden' name='cmd' value='_xclick'>
                                       <input type='hidden' name='charset' value='utf-8' />
                                       <input type='hidden' name='business' value='" . $paypal_email . "'>
                                       <input type='hidden' name='item_id' value='" . $webform->getId() . "' />
                                       <input type='hidden' name='item_number' value='" . $result["success"] . "' />
                                       <input type='hidden' name='item_name' value='" . $webform->getName() . "'>
                                       <input type='hidden' name='amount' value='" . $price . "'>
                                       <input type='hidden' name='currency_code' value='" . $curr_code . "'>
                                       <input type='hidden' name='bn' value='Business_BuyNow_WPS_SE' />
                                       <input type='hidden' name='cancel_return' id='cancel_return' value='" . $url . "?web_en=" . $encode_url_str . "' />
                                       <input type='hidden' name='notify_url' value='" . $this->getRequest()->getPost("ret_url") . "' />
                                       <input type='hidden' name='return' id='return' value='" . $this->getRequest()->getPost("ret_url") . "' />
                                       <input type='submit' name='btn_submit' id='btn_submit' style='display:none' />
                                     </form><script type='text/javascript'>jQuery(document).ready(function(){jQuery('.custom-tab-title').text('Payment'); jQuery('#frm_paypal_submit').submit();});</script>";
                    }
                    $resultSuccess = $result["success"];
                    $result = $this->getSuccessData($chkExpress, $webform, $html_data, $result, $resultSuccess, $express_delivery_taken);
                } else if ($chkExpress == 0) {
                    $html_data = "";
                    $resultSuccess = $result["success"];
                    $express_delivery_taken = false;
                    $result = $this->getSuccessData($chkExpress, $webform, $html_data, $result, $resultSuccess, $express_delivery_taken);
                } else {
                    $errors = $this->messageManager->getMessages(true)->getItems();
                    foreach ($errors as $err) {
                        $result["errors"][] = $err->getText();
                    }
                    $html_errors = "";
                    if (count($result["errors"]) > 1) {
                        foreach ($result["errors"] as $err) {
                            $html_errors .= '<p>' . $err . '</p>';
                        }
                        $result["errors"] = $html_errors;
                    } else {
                        $result["errors"] = $result["errors"][0];
                    }
                }

                if (!$webform->getIsActive()) {
                    $result["errors"][] = __('Web-form is not active.');
                }

                $json = $this->_jsonEncoder->encode($result);
                $resultHttp = $this->resultHttpFactory->create();
                $resultHttp->setNoCacheHeaders();
                $resultHttp->setHeader('Content-Type', 'text/plain', true);
                return $resultHttp->setContent($json);
            }
        }
        // Regular submit
        if ($this->getRequest()->getParam('submitForm_' . $webform->getId()) && $webform->getIsActive()) {
            //Validate
            $result = $webform->savePostResult();
            if ($result) {
                $this->_customerSession = $this->_customerSessionFactory->create();
                $this->_customerSession->setFormSuccess($webform->getId());
                $this->_customerSession->setData('webform_result_' . $webform->getId(), $result->getId());
            }

            $url = $this->header->getHttpReferer();

            if ($webform->getRedirectUrl()) {
                if (strstr($webform->getRedirectUrl(), '://')) {
                    $url = $webform->getRedirectUrl();
                } else {
                    $url = $this->getUrl($webform->getRedirectUrl());
                }
            }

            return $this->_response->setRedirect($url);

        }
        $resultLayout = $this->resultPageFactory->create();
        $resultLayout->setStatusHeader(404, '1.1', 'Not Found');
        $resultLayout->setHeader('Status', '404 File not found');
        return $resultLayout;
    }
    /**
     * Get Success Result
     */
    public function getSuccessData($chkExpress, $webform, $html_data, $result, $resultSuccess, $express_delivery_taken)
    {
        if ($html_data == "") {
            $result["success_text"] = $webform->getSuccessText();
        } else {
            $result["success_text"] = $html_data;
        }

        $resultObject = $this->_resultFactory->create()->load($resultSuccess);

        if ($resultObject) {
            if ($webform->getSuccessText()) {
                $result["success_text"] = $webform->getSuccessText();
            }
            // Apply custom variables
            $filter = $this->_filterProvider->getPageFilter();
            $webformObject = new \Magento\Framework\DataObject;
            $webformObject->setData($webform->getData());
            $subject = $resultObject->getEmailSubject('customer');
            $filter->setVariables(array(
                'webform_result' => $resultObject->toHtml('customer'),
                'result' => $resultObject->getTemplateResultVar(),
                'webform' => $webformObject,
                'webform_subject' => $subject,
            ));

            if ($chkExpress == 1) {
                if ($html_data == "") {
                    $result["success_text"] = $webform->getSuccessText();
                } else {
                    $result["success_text"] = $html_data;
                }
            } elseif ($chkExpress == 0) {
                $result["success_text"] = "&nbsp;";
                if ($webform->getSuccessText()) {
                    $result["success_text"] = $filter->filter($webform->getSuccessText());
                }
            } else {
                if ($webform->getSuccessText()) {
                    $result["success_text"] = $webform->getSuccessText();
                }
            }
            if ($webform->getRedirectUrl()) {
                if (strstr($webform->getRedirectUrl(), '://')) {
                    $redirectUrl = $webform->getRedirectUrl();
                } else {
                    $redirectUrl = $this->_url->getUrl($webform->getRedirectUrl());
                }

                $result["redirect_url"] = $redirectUrl;
            }
            //if ($this->getRequest()->getPost('post_data_id') != "") {
            $fields = $this->_fieldFactory->create()->getCollection()->addFieldToFilter('webform_id', $webform->getId());
            $results = $this->_resultFactory->create()->load($resultSuccess);
            //if ($express_delivery_taken == false) {
            $webform->sendFormMail($results, $fields);
            //  }
            //}
        }
        return $result;
    }

    /**
     * @param array $fieldData
     * @return string
     */
    public function checkDateFormat($fieldData){
        try {
            foreach ($fieldData as $fiedDataKey => $fiedDataValue) {
                $field = $this->_fieldFactory->create()->load($fiedDataKey);
                if (is_array($fiedDataValue)) {
                    $value = implode("\n", $fiedDataValue);
                }
                if (strstr($field->getType(), 'date')) {
                    // check if the date is already in db format
                    $format_date = true;
                    if (date('Y-m-d H:i:s', strtotime($value)) == $value || date('Y-m-d', strtotime($value)) == $value) {
                        $format_date = false;
                    }
                    if ($format_date) {
                        if (strlen($value) > 0) {
                            $dateFormat = $field->getDateFormat();
                            if ($field->getType() == 'datetime')
                                $dateFormat .= " " . $field->getTimeFormat();
                            $dateArray = \Zend_Locale_Format::getDateTime($value, [
                                'date_format' => $dateFormat
                            ]);
                        }
                    }
                }
            }
            $result = "TRUE";
        }catch (\Exception $exception){
            $result =  $exception->getMessage();
        }

        return $result;
    }
}
