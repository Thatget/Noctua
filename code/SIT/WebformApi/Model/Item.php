<?php

namespace SIT\WebformApi\Model;

use SIT\WebformApi\Api\ItemInterface;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class Item implements ItemInterface
{

    private $_jsonHelperData;
    protected $date;
    protected $timezone;
    protected $_filesystem;
    protected $webformResultFactory;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Json\Helper\Data $jsonHelperData,
        \VladimirPopov\WebForms\Model\ResultFactory $webformResultFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_jsonHelperData = $jsonHelperData;
        $this->timezone = $timezone;
        $this->_filesystem = $filesystem;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->webformResultFactory = $webformResultFactory;
        $this->date = $date;
    }
    /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $createddate
     * @return array
     */
    public function items($createddate)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $createddate)) {
            $createddate = $this->date->gmtDate('Y-m-d');
        }
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $arr_webforms = array();
        $final_date = $this->timezone->date(new \DateTime($createddate))->format('Y-m-d');
        $webform_name = "";
        $webforms_results = $objectManager->get('VladimirPopov\WebForms\Model\Field');
        $webforms_webformname = $objectManager->get('VladimirPopov\WebForms\Model\Form');
        $_chk_use_in_soap = $webforms_webformname->getCollection()->addFieldToFilter('use_in_soap', 1);

        if ($_chk_use_in_soap) {
            $_form_ids = array();
            foreach ($_chk_use_in_soap as $key => $value) {
                $_form_ids[] = $value->getId();
            }
            $c_date = $this->date->gmtDate('Y-m-d');
            $resultData = $this->webformResultFactory->create()->getCollection();

            $webforms = $this->webformResultFactory->create()->getCollection()->addFieldToFilter('created_time', array('from' => $final_date . " 00:00:00"))->addFieldToFilter('created_time', array('to' => $c_date . " 23:59:59"))->addFieldToFilter('webform_id', array('in' => $_form_ids));
            foreach ($webforms as $webform) {
                $webforms_collection = $webforms_results->getCollection()->addFieldToFilter('webform_id', $webform->getWebformId());
                $form_code = $webforms_webformname->load($webform->getWebformId());
                if ($form_code->getCode() != "") {
                    $webform_name = $form_code->getCode();
                } else {
                    $webform_name = $form_code->getName();
                }

                $tryexp = 0;
                $trypay = 0;
                $allexp = 0;
                $allpay = 0;
                $exp_exist = 0;
                $pay_exist = 0;
                $temp = 0;

                foreach ($webforms_collection as $webresultchk) {
                    $idchk = $webresultchk->getId();
                    if ($webresultchk->getCode() != "") {
                        $code_valuechk = $webresultchk->getCode();
                    } else {
                        $code_valuechk = $webresultchk->getName();
                    }
                    if ($code_valuechk == 'payment_confirmed') {
                        $pay_exist = 1;
                    }

                    if ($code_valuechk == 'express_delivery') {
                        $fieldchk = "getField" . $idchk;
                        $valuechk = $webform->$fieldchk();
                        if ($code_valuechk == 'express_delivery' && $valuechk == 'No') {
                            $trypay = 1;
                            $temp = 1;
                        }
                    }
                    if ($code_valuechk == 'payment_confirmed') {
                        $fieldchk = "getField" . $idchk;
                        $valuechk = $webform->$fieldchk();
                        if ($code_valuechk == 'payment_confirmed' && $valuechk == '') {$trypay = 1;}
                        if ($code_valuechk == 'payment_confirmed' && $valuechk == 'Yes') {
                            $trypay = 1;
                        }
                        if ($code_valuechk == 'payment_confirmed' && $valuechk == 'No') {
                            if ($temp == 0) {
                                $trypay = 0;
                            }if ($temp == 1) {
                                $trypay = 1;
                            }
                        }
                    }
                }
                if ($pay_exist == 0) {$trypay = 1;}
                if ($trypay == 1) {
                    $fields_arr = array();
                    foreach ($webforms_collection as $webresult) {
                        $id = $webresult->getId();
                        if ($webresult->getCode() != "") {
                            $code_value = $webresult->getCode();
                        } else {
                            $code_value = $webresult->getName();
                        }

                        /*$field = "getField" . $id;
                        $value = $webform->$field();
                         */
                        /*New change for get web-form result[START]*/
                        $value = [];
                        $fileTable = $this->resource->getTableName('webforms_files');
                        if ($webresult->getType() == "file") {
                            /*    $key = "getKey" . $id;
                            $value = $mediaUrl . "webforms/" . $webform->getId() . "/" . $id . "/" . $webform->$key() . "/" . $value;*/
                            $select = $this->connection->select()
                                ->from($fileTable)
                                ->where('result_id = ?', $webform->getId())
                                ->where('field_id = ?', $id);
                            $result_value = $this->connection->fetchAll($select);
                            if ($result_value) {
                                foreach ($result_value as $item) {
                                    /*$value[] = $mediaUrl . "webforms/" . $webform->getId() . "/" . $id . "/" . $item['link_hash'] . "/" . $item['name'];*/
                                    $value[] = $mediaUrl . $item['path'];
                                }
                            }
                        }

                        $resultTable = $this->resource->getTableName('webforms_results_values');
                        $select = $this->connection->select()
                            ->from($resultTable)
                            ->where('result_id = ?', $webform->getId())
                            ->where('field_id = ?', $id);

                        $result_value = $this->connection->fetchAll($select);
                        foreach ($result_value as $item) {
                            if ($webresult->getType() != "file") {
                                $value[] = $item['value'];
                            }
                        }
                        if (is_array($value)) {
                            $value = implode("\n", $value);
                        }
                        /*New change for get web-form result[END]*/

                        $fields_arr[] = array('code' => $code_value, 'value' => $value);
                    }
                    $arr_webforms[] = array('id' => $webform->getId(), 'name' => $webform_name, 'store_id' => $webform->getStoreId(), 'data' => $fields_arr);
                }
            }
        }
        return $arr_webforms;
    }
}
