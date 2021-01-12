<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [1-3-2019]
 */
namespace SIT\ProductTechNew\Controller\Adminhtml\ProductTechnology;

use Magento\Backend\App\Action;
use SIT\ProductTechNew\Model\ProductTechnologyFactory;

class Duplicate extends Action {
	/**
	 * @var \SIT\ProductTechNew\Model\ProductFactory
	 */
	protected $modelProductTech;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $date;

	/**
	 * @param Action\Context                              $context                 [description]
	 * @param \Magento\Framework\App\ResourceConnection   $resourceConnection      [description]
	 * @param ProductTechnologyFactory                    $modelProductTech        [description]
	 * @param \SIT\ProductTechNew\Model\ProductFactory    $modelProductTechProduct [description]
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime $date                    [description]
	 */
	public function __construct(
		Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		ProductTechnologyFactory $modelProductTech,
		\SIT\ProductTechNew\Model\ProductFactory $modelProductTechProduct,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
	) {
		parent::__construct($context);
		$this->resourceConnection = $resourceConnection;
		$this->modelProductTech = $modelProductTech;
		$this->modelProductTechProduct = $modelProductTechProduct;
		$this->date = $date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$editDuplicate = $this->getRequest()->getParam('edit_duplicate');
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($id) {
			try {
				$model = $this->modelProductTech->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
				$newUrlKey = $this->checkUrlKey($model->getUrlKey());
				$products = $this->modelProductTechProduct->create()->getCollection()->addFieldToFilter("producttechnology_id", ["eq" => $model->getEntityId()]);

				$duplicateData = $model->getData();
				$duplicateData["url_key"] = $newUrlKey;
				$duplicateData["created_at"] = $this->date->gmtDate();
				array_shift($duplicateData);
				$this->createDuplicate($duplicateData, $products->getData());
				$this->messageManager->addSuccess(__('Duplicate generated'));
				$lastId = $this->modelProductTech->create()->getCollection()->getLastItem();
				if ($editDuplicate == 1) {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				} else {
					return $resultRedirect->setPath('*/*/edit', ['entity_id' => $lastId->getEntityId()]);
				}
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
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
		$model = $this->modelProductTech->create();
		$model->setData($data);
		$newQuestionId = $model->save();
		$this->insertProductIds($model->getEntityId(), $products);
	}

	/**
	 * generate url key
	 *
	 * @param  [string] $urlKey [description]
	 * @return  string        [description]
	 */
	private function checkUrlKey($urlKey) {
		$model = $this->modelProductTech->create()->getCollection()->addFieldToFilter("url_key", ["like" => "%" . $urlKey . "%"])->setOrder("producttechnology_id", "DESC")->getFirstItem();
		$urlKey = $model->getUrlKey();

		return $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
		? $matches[1] . '-' . ($matches[2] + 1)
		: $urlKey . '-1';
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
				$fields = ["producttechnology_id" => $questionId, "product_id" => $value["product_id"], "position" => $value["position"]];
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
		$save = $this->modelProductTechProduct->create();
		$save->setData($productArray);
		$save->save();
	}
}
