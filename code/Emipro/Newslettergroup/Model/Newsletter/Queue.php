<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Model\Newsletter;

class Queue extends \Magento\Newsletter\Model\Queue
{
    public function sendPerSubscriber($count = 20)
    {
        $customer_group_id = $this->getUserGroup();
        /**
         * Queue log set by custom. Please do not remove it
         */
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/newsletter_queue_' . $this->getQueueId() . '(GroupId:' . $customer_group_id . ').log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if (
            $this->getQueueStatus() != self::STATUS_SENDING &&
            ($this->getQueueStatus() != self::STATUS_NEVER &&
                $this->getQueueStartAt())
        ) {
            return $this;
        }

        if (!$this->_subscribersCollection->getQueueJoinedFlag()) {
            $this->_subscribersCollection->useQueue($this);
        }

        if ($this->_subscribersCollection->getSize() == 0) {
            $this->_finishQueue();
            return $this;
        }

        $collection = $this->_subscribersCollection->useOnlyUnsent()->showCustomerInfo()->setPageSize(
            $count
        )->setCurPage(
            1
        );

        if ($customer_group_id == 0) {
            $this->_finishQueue();
            return $this;
            // $collection;
        } else {
            $collection->getSelect()->join(array('eusersub' => 'emipro_newsletter_user_subscriber'), 'main_table.subscriber_id=eusersub.sub_id AND eusersub.group_id=' . $customer_group_id)->group('main_table.subscriber_id');
        }
        /**
         * Queue log set by custom. Please do not remove it
         */
        foreach ($collection as $key => $value) {
            $logger->info(print_r($value->getSubscriberEmail(), true));
        }

        $tBody = $this->getNewsletterText();
        // $tType = $this->getNewsletterType(); //tuanta
        $tTemplate = $this->getTemplate();
        $tType = $tTemplate->getType();
        $tTemText = $tBody;
        // if ($tType !== self::TYPE_HTML)
        //     $tTemText = nl2br($tBody);

        $this->_transportBuilder->setTemplateData(
            [
                'template_subject' => $this->getNewsletterSubject(),
                'template_text' => $tTemText,
                'template_styles' => $this->getNewsletterStyles(),
                'template_filter' => $this->_templateFilter,
                'template_type' => $tType, //self::TYPE_HTML,
            ]
        );
        // $logger->info(print_r($tTemText, true));
        // $logger->info(print_r($tType, true));
        // $logger->info(print_r($this->getNewsletterSenderName(), true));
        // $senderName = $this->getNewsletterSenderName();
        // $replaceSymbols =  str_split( \Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match::SPECIAL_CHARACTERS, 1);
        // $senderName = str_replace($replaceSymbols, '', $senderName);

        foreach ($collection->getItems() as $item) {
            $transport = $this->_transportBuilder->setTemplateOptions(
                ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $item->getStoreId()]
            )->setTemplateVars(
                ['subscriber' => $item]
            )->setFrom(
                ['name' => $this->getNewsletterSenderName(), 'email' => $this->getNewsletterSenderEmail()]
            )->addTo(
                $item->getSubscriberEmail(),
                $item->getSubscriberFullName()
            )->getTransport();

            try {
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $e) {

                $problem = $this->_problemFactory->create();
                $problem->addSubscriberData($item);
                $problem->addQueueData($this);
                $problem->addErrorData($e);
                $problem->save();
            }
            $item->received($this);
        }

        if (count($collection->getItems()) < $count - 1 || count($collection->getItems()) == 0) {
            $this->_finishQueue();
        }
        return $this;
    }
}
