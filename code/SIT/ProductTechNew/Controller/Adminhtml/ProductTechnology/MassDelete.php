<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductTechNew\Controller\Adminhtml\ProductTechnology;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductTechNew\Model\ResourceModel\ProductTechnology\Collection;

/**
 * Class MassDelete
 */
class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Collection
     */
    protected $_productTechnologyCollection;

    /**
     * [__construct description]
     * @param Context    $context                 [description]
     * @param Filter     $filter                  [description]
     * @param Collection $productTechnologyCollection [description]
     */
    public function __construct(Context $context, Filter $filter, Collection $productTechnologyCollection)
    {
        $this->filter = $filter;
        $this->_productTechnologyCollection = $productTechnologyCollection;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->_productTechnologyCollection);
        $collectionSize = $collection->getSize();
        $collection->walk('delete');

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
