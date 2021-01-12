<?php

namespace SIT\ProductReviewNew\Controller\ProductReview;

use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Framework\App\Action\Action
{
    protected $resultFactory;

    /**
     * Action constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ResultFactory $resultFactory
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
    }

    public function execute()
    {
        return $this->initResultPage();
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function initResultPage()
    {
         /** @var \Magento\Framework\View\Result\Page $resultPage */
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Review - '));
        $this->_view->renderLayout();
    }
}
