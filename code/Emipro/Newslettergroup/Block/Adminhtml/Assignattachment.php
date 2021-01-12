<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [15-04-2019]
 */

namespace Emipro\Newslettergroup\Block\Adminhtml;

class Assignattachment extends \Magento\Backend\Block\Template
{
    /**
     * @var Template
     */
    protected $_template = 'assign_attachment.phtml';

    /**
     * @var blockGrid
     */
    protected $blockGrid;

    /**
     * @var \Emipro\Newslettergroup\Model\Newsletter
     */
    protected $newsLetter;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context   $context            [description]
     * @param \Magento\Framework\Json\EncoderInterface  $jsonEncoder        [description]
     * @param \Emipro\Newslettergroup\Model\Newsletter  $newsLetter         [description]
     * @param \Magento\Newsletter\Model\Subscriber      $collectionFactory  [description]
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection [description]
     * @param array                                     $data               [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Emipro\Newslettergroup\Model\Newsletter $newsLetter,
        \Magento\Newsletter\Model\Subscriber $collectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->newsLetter = $newsLetter;
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Emipro\Newslettergroup\Block\Adminhtml\Assignattachment\Attachgrid'
            );
        }
        return $this->blockGrid;
    }
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }
    public function getAttachmentJson()
    {
        if ($this->getRequest()->getParam('id')) {
            $product_id = $this->getRequest()->getParam('id');
        } else {

            $groupData = $this->newsLetter->getCollection();
            $product_id = null;
            foreach ($groupData as $key => $valueGroup) {
                $product_id = $valueGroup->getId() + 1;
            }
        }

        $storeId = $this->getRequest()->getParam('store', 0);

        $products = $this->collectionFactory->getCollection();

        $tableName = $this->resourceConnection;
        $protable = $tableName->getTableName('emipro_newsletter_user_subscriber');

        if ($product_id) {
            $products->getSelect()->joinRight(array('prodoctable' => $protable), 'prodoctable.sub_id=main_table.subscriber_id AND prodoctable.group_id=' . $product_id, array('*'));
            $products->getSelect()->group('main_table.subscriber_id');
        }

        foreach ($products as $value) {
            $data[$value->getId()] = $value->getId();
        }
        if (!empty($data)) {
            return $this->jsonEncoder->encode($data);
        }

        return '{}';
    }
}
