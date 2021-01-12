<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductFaqNew\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductFaqNew\Model\ResourceModel\Category\Collection;

/**
 * Class MassDelete
 */
class MassDelete extends Action {
	/**
	 * @var Filter
	 */
	protected $filter;

	/**
	 * @var Collection
	 */
	protected $faqCatCollection;

	/**
	 * @param Context    $context          [description]
	 * @param Filter     $filter           [description]
	 * @param Collection $faqCatCollection [description]
	 */
	public function __construct(Context $context, Filter $filter, Collection $faqCatCollection) {
		$this->filter = $filter;
		$this->faqCatCollection = $faqCatCollection;
		parent::__construct($context);
	}

	/**
	 * Execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 * @throws \Magento\Framework\Exception\LocalizedException|\Exception
	 */
	public function execute() {
		$collection = $this->filter->getCollection($this->faqCatCollection);
		$collectionSize = $collection->getSize();
		$collection->walk('delete');

		$this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		return $resultRedirect->setPath('*/*/');
	}
}
