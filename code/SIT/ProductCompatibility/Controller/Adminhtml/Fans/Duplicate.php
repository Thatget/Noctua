<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [22-05-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Fans;

use Magento\Backend\App\Action;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;

class Duplicate extends Action {
	/**
	 * @var ProductCompatibilityFactory
	 */
	protected $productcompFactory;

	/**
	 * @var \SIT\ProductCompatibility\Model\ProductFactory
	 */
	protected $compProductFactory;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $date;

	/**
	 * @param Action\Context                                 $context            [description]
	 * @param \Magento\Framework\App\ResourceConnection      $resourceConnection [description]
	 * @param ProductCompatibilityFactory                    $productcompFactory [description]
	 * @param \SIT\ProductCompatibility\Model\ProductFactory $compProductFactory [description]
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime    $date               [description]
	 */
	public function __construct(
		Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		ProductCompatibilityFactory $productcompFactory,
		\SIT\ProductCompatibility\Model\ProductFactory $compProductFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
	) {
		parent::__construct($context);
		$this->resourceConnection = $resourceConnection;
		$this->productcompFactory = $productcompFactory;
		$this->compProductFactory = $compProductFactory;
		$this->date = $date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('entity_id');
		$howMany = $this->getRequest()->getParam('howmany');
		$withProduct = $this->getRequest()->getParam('withproduct');
		$editDuplicate = $this->getRequest()->getParam('edit_duplicate');
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($id) {
			try {
				$model = $this->productcompFactory->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $id])->getFirstItem();
				$newUrlKey = $this->checkUrlKey($model->getUrlKey());
				$products = $this->compProductFactory->create()->getCollection()->addFieldToFilter("productcompatibility_id", ["eq" => $model->getEntityId()]);

				$duplicateData = $model->getData();
				$duplicateData["url_key"] = $newUrlKey;
				$duplicateData["created_at"] = $this->date->gmtDate();
				array_shift($duplicateData);
				if ($howMany != '') {
					for ($i = 1; $i <= $howMany; $i++) {
						if ($withProduct != '') {
							$this->createDuplicate($duplicateData, $products->getData());
						} else {
							$this->duplicateWithoutProduct($duplicateData);
						}
						$lastIdData = $this->productcompFactory->create()->getCollection()->addAttributeToselect('url_key')->getLastItem();
						$newUrlKeyData = $this->checkUrlKey($lastIdData->getUrlKey());
						$duplicateData["url_key"] = $newUrlKeyData;
					}
				} else {
					$this->createDuplicate($duplicateData, $products->getData());
				}
				$this->messageManager->addSuccess(__('Duplicate generated'));
				$lastId = $this->productcompFactory->create()->getCollection()->getLastItem();
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
		$model = $this->productcompFactory->create();
		$model->setData($data);
		$newQuestionId = $model->save();
		$this->insertProductIds($model->getEntityId(), $products);
	}

	/**
	 * Create duplicate without product
	 * @param  [type] $data     [description]
	 */
	private function duplicateWithoutProduct($data) {
		$model = $this->productcompFactory->create();
		$model->setData($data);
		$newQuestionId = $model->save();
	}

	/**
	 * generate url key
	 *
	 * @param  [string] $urlKey [description]
	 * @return  string        [description]
	 */
	private function checkUrlKey($urlKey) {
		$model = $this->productcompFactory->create()->getCollection()->addFieldToFilter("url_key", ["like" => "%" . $urlKey . "%"])->setOrder("productcompatibility_id", "DESC")->getFirstItem();
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
				$fields = ["productcompatibility_id" => $questionId, "product_id" => $value["product_id"], "position" => $value["position"]];
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
		$save = $this->compProductFactory->create();
		$save->setData($productArray);
		$save->save();
	}
}
