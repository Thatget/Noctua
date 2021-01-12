<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductVideoNew\Controller\Adminhtml\ProductVideo;

use Magento\Backend\App\Action;

class Duplicate extends Action {
	/**
	 * @var \SIT\ProductVideoNew\Model\ProductVideoFactory
	 */
	protected $productVideo;

	/**
	 * @var \SIT\ProductVideoNew\Model\ProductFactory
	 */
	protected $product;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $date;

	/**
	 * @param Action\Context                                 $context            [description]
	 * @param \Magento\Framework\App\ResourceConnection      $resourceConnection [description]
	 * @param \SIT\ProductVideoNew\Model\ProductVideoFactory $productVideo       [description]
	 * @param \SIT\ProductVideoNew\Model\ProductFactory      $product            [description]
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime    $date               [description]
	 */
	public function __construct(
		Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\SIT\ProductVideoNew\Model\ProductVideoFactory $productVideo,
		\SIT\ProductVideoNew\Model\ProductFactory $product,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
	) {
		parent::__construct($context);
		$this->resourceConnection = $resourceConnection;
		$this->product = $product;
		$this->productVideo = $productVideo;
		$this->date = $date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($id) {
			try {
				$model = $this->productVideo->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
				$products = $this->product->create()->getCollection()->addFieldToFilter("productvideo_id", ["eq" => $model->getEntityId()]);

				$duplicateData = $model->getData();
				$duplicateData["created_at"] = $this->date->gmtDate();
				array_shift($duplicateData);
				$this->createDuplicate($duplicateData, $products->getData());
				$this->messageManager->addSuccess(__('Duplicate generated'));
				$editDuplicate = $this->getRequest()->getParam('edit_duplicate');
				$lastId = $this->productVideo->create()->getCollection()->getLastItem();
				if ($editDuplicate == 1) {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				} else {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				}
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				return $resultRedirect->setPath('*/*/edit', ['entity_id ' => $id]);
			}
		}
		$this->messageManager->addError(__('Record does not exist'));
		return $resultRedirect->setPath('*/*/');
	}

	/**
	 * Create duplicate from main table
	 *
	 * @param  [type] $data     [description]
	 * @param  [type] $products [description]
	 * @return [type]           [description]
	 */
	private function createDuplicate($data, $products) {
		$model = $this->productVideo->create();
		$model->setData($data);
		$newQuestionId = $model->save();
		$this->insertProductIds($model->getEntityId(), $products);
	}

	/**
	 * Loop of product ids
	 *
	 * @param  [type] $questionId [description]
	 * @param  [type] $products   [description]
	 * @return [type]             [description]
	 */
	private function insertProductIds($questionId, $products) {
		try {
			foreach ($products as $key => $value) {
				$fields = ["productvideo_id" => $questionId, "product_id" => $value["product_id"], "position" => $value["position"]];
				$this->insertProduct($fields);
			}
		} catch (\Magento\Framework\Validator\Exception $e) {
			$this->messageManager->addError('Product Id not Saved.');
		}
	}

	/**
	 * insert selected products
	 *
	 * @param  [type] $productArray [description]
	 * @return [type]               [description]
	 */
	private function insertProduct($productArray) {
		$save = $this->product->create();
		$save->setData($productArray);
		$save->save();
	}
}
