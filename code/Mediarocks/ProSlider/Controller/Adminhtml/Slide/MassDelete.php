<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [29-03-2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mediarocks\ProSlider\Model\ResourceModel\Slide\Collection;

/**
 * MassDelete Records File
 */
class MassDelete extends Action {
	/**
	 * @var Filter
	 */
	protected $filter;

	/**
	 * @var Collection
	 */
	protected $slideCollection;

	/**
	 * @param Context    $context        [description]
	 * @param Filter     $filter         [description]
	 * @param Collection $slideCollection [description]
	 */
	public function __construct(
		Context $context,
		Filter $filter,
		Collection $slideCollection
	) {
		$this->filter = $filter;
		$this->slideCollection = $slideCollection;
		parent::__construct($context);
	}

	/**
	 * Execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 * @throws \Magento\Framework\Exception\LocalizedException|\Exception
	 */
	public function execute() {
		$collection = $this->filter->getCollection($this->slideCollection);
		$collectionSize = $collection->getSize();
		$collection->walk('delete');

		$this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		return $resultRedirect->setPath('*/*/');
	}
}
