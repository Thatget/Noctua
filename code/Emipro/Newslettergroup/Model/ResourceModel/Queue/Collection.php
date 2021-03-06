<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Model\ResourceModel\Queue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var boolean
     */
    protected $_addSubscribersFlag = false;

    /**
     * @var boolean
     */
    protected $_isStoreFilter = false;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * [__construct description]
     * @param \Magento\Framework\Data\Collection\EntityFactory             $entityFactory [description]
     * @param \Psr\Log\LoggerInterface                                     $logger        [description]
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy [description]
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager  [description]
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                  $date          [description]
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection    [description]
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource      [description]
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_date = $date;
    }

    protected function _construct()
    {
        $this->_map['fields']['queue_id'] = 'main_table.queue_id';
        $this->_init('Magento\Newsletter\Model\Queue', 'Magento\Newsletter\Model\ResourceModel\Queue');
    }
    public function addTemplateInfo()
    {
        $this->getSelect()->joinLeft(
            ['template' => $this->getTable('newsletter_template')],
            'template.template_id=main_table.template_id',
            ['template_subject', 'template_sender_name', 'template_sender_email']
        );
        $this->_joinedTables['template'] = true;
        return $this;
    }
    protected function _addSubscriberInfoToSelect()
    {
        $select = $this->getConnection()->select()
            ->from(['qlt' => $this->getTable('newsletter_queue_link')],
                'COUNT(qlt.queue_link_id)')
            ->where('qlt.queue_id = main_table.queue_id')
            ->where('main_table.user_group in (select group_id from ' . $this->getTable("emipro_newsletter_user_subscriber") .
                ' where sub_id in (select subscriber_id from ' . $this->getTable("newsletter_subscriber") . ' where subscriber_id=qlt.subscriber_id))');
        $totalExpr = new \Zend_Db_Expr(sprintf('(%s)', $select->assemble()));
        $select = $this->getConnection()->select()
            ->from(['qls' => $this->getTable('newsletter_queue_link')],
                'COUNT(qls.queue_link_id)')
            ->where('qls.queue_id = main_table.queue_id')
            ->where('main_table.user_group in (select group_id from ' . $this->getTable("emipro_newsletter_user_subscriber") .
                ' where sub_id in (select subscriber_id from ' . $this->getTable("newsletter_subscriber") . ' where subscriber_id=qls.subscriber_id))')
            ->where('qls.letter_sent_at IS NOT NULL');
        $sentExpr = new \Zend_Db_Expr(sprintf('(%s)', $select->assemble()));

        $this->getSelect()->columns(['subscribers_sent' => $sentExpr, 'subscribers_total' => $totalExpr]);

        return $this;
    }
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->_addSubscribersFlag && !$this->isLoaded()) {
            $this->_addSubscriberInfoToSelect();
        }
        return parent::load($printQuery, $logQuery);
    }
    public function addSubscribersInfo()
    {
        $this->_addSubscribersFlag = true;
        return $this;
    }
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, ['subscribers_total', 'subscribers_sent'])) {
            $this->addFieldToFilter('main_table.queue_id', ['in' => $this->_getIdsFromLink($field, $condition)]);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }
    protected function _getIdsFromLink($field, $condition)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('newsletter_queue_link'),
            ['queue_id', 'total' => new \Zend_Db_Expr('COUNT(queue_link_id)')]
        )->group(
            'queue_id'
        )->having(
            $this->_getConditionSql('total', $condition)
        );

        if ($field == 'subscribers_sent') {
            $select->where('letter_sent_at IS NOT NULL');
        }

        $idList = $this->getConnection()->fetchCol($select);

        if (count($idList)) {
            return $idList;
        }

        return [0];
    }
    public function addSubscriberFilter($subscriberId)
    {
        $this->getSelect()->join(
            ['link' => $this->getTable('newsletter_queue_link')],
            'main_table.queue_id=link.queue_id',
            ['letter_sent_at']
        )->where(
            'link.subscriber_id = ?',
            $subscriberId
        );

        return $this;
    }
    public function addOnlyForSendingFilter()
    {
        $this->getSelect()->where(
            'main_table.queue_status in (?)',
            [\Magento\Newsletter\Model\Queue::STATUS_SENDING, \Magento\Newsletter\Model\Queue::STATUS_NEVER]
        )->where(
            'main_table.queue_start_at < ?',
            $this->_date->gmtdate()
        )->where(
            'main_table.queue_start_at IS NOT NULL'
        );

        return $this;
    }
    public function addOnlyUnsentFilter()
    {
        $this->addFieldToFilter('main_table.queue_status', \Magento\Newsletter\Model\Queue::STATUS_NEVER);

        return $this;
    }
    public function toOptionArray()
    {
        return $this->_toOptionArray('queue_id', 'template_subject');
    }
    public function addStoreFilter($storeIds)
    {
        if (!$this->_isStoreFilter) {
            $this->getSelect()->joinInner(
                ['store_link' => $this->getTable('newsletter_queue_store_link')],
                'main_table.queue_id = store_link.queue_id',
                []
            )->where(
                'store_link.store_id IN (?)',
                $storeIds
            )->group(
                'main_table.queue_id'
            );
            $this->_isStoreFilter = true;
        }
        return $this;
    }
}
