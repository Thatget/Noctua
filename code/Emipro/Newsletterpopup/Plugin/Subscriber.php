<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [10-05-2019]
 */

namespace Emipro\Newsletterpopup\Plugin;

use Magento\Framework\App\Request\Http;

class Subscriber
{
    /**
     * @var Http
     */
    protected $request;

    /**
     * @var Magento\Catalog\Model\ResourceModel\ProductFactory
     */
    protected $_attributeLoading;

    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $country;

    /**
     * [__construct description]
     * @param Http                                                $request          [description]
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory $attributeLoading [description]
     * @param \Magento\Directory\Model\Country                    $country          [description]
     */
    public function __construct(
        Http $request,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $attributeLoading,
        \Magento\Directory\Model\Country $country
    ) {
        $this->request = $request;
        $this->_attributeLoading = $attributeLoading;
        $this->country = $country;
    }

    public function aroundSubscribe($subject, \Closure $proceed, $email)
    {
        /**
         * Get source_website label
         */
        $poductReource = $this->_attributeLoading->create();
        $attr = $poductReource->getAttribute('newsletter_website');

        $press = $this->request->getPost('Press');
        $manual = $this->request->getPost('Manual');
        $endCustomer = 'End-Customer';
        /**
         * Get Country and State
         */
        $country = $this->request->getPost('country-dropdown');
        $state = $this->request->getPost('state-dropdown');

        if ($this->request->isPost()) {
            if ($press) {
                $optionId = $attr->getSource()->getOptionId($press); //Press Source Website
            } else if ($manual) {
                $optionId = $attr->getSource()->getOptionId($manual); //Manual Source Website
            } else {
                $optionId = $attr->getSource()->getOptionId($endCustomer); //End-Customer Source Website
            }
            /**
             * Set Source Website
             */
            $subject->setSourceWebsite($optionId);
            /**
             * Set Country and State
             */
            if ($country) {
                $countryName = $this->country->loadByCode($country);
                $subject->setSubscriberCountry($countryName->getName());
            }
            if ($state) {
                $subject->setSubscriberState($state);
            }
            /**
             * Changes MD for set subscription date[START][25-06-2019]
             */
            $subject->setSubscriptionDate(date("d-m-Y H:i:s"));
            /**
             * Changes MD for set subscription date[END][25-06-2019]
             */

            $result = $proceed($email);

            try {
                $subject->save();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $result;
    }
}
