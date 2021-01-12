<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [1-3-2019]
 */
namespace SIT\ProductTechNew\Controller\Adminhtml\ProductTechnology;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SIT\ProductTechNew\Model\Product;
use SIT\ProductTechNew\Model\ResourceModel\ProductTechnology\CollectionFactory;

class MassDuplicate extends \Magento\Backend\App\Action {
	/**
	 * @var Filter
	 */
	protected $filter;
	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;
	/**
	 * [__construct description]
	 * @param Context                                            $context                 [description]
	 * @param Filter                                             $filter                  [description]
	 * @param CollectionFactory                                  $collectionFactory       [description]
	 * @param \Magento\Framework\App\ResourceConnection          $resourceConnection      [description]
	 * @param \SIT\ProductTechNew\Model\ProductTechnologyFactory $modelProductTech        [description]
	 * @param \SIT\ProductTechNew\Model\ProductFactory           $modelProductTechProduct [description]
	 */
	public function __construct(Context $context,
		Filter $filter,
		CollectionFactory $collectionFactory,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\SIT\ProductTechNew\Model\ProductTechnologyFactory $modelProductTech,
		\SIT\ProductTechNew\Model\ProductFactory $modelProductTechProduct
	) {
		$this->filter = $filter;
		$this->collectionFactory = $collectionFactory;
		$this->resourceConnection = $resourceConnection;
		$this->modelProductTech = $modelProductTech;
		$this->modelProductTechProduct = $modelProductTechProduct;
		parent::__construct($context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$jobData = $this->collectionFactory->create();

		foreach ($jobData as $value) {
			$templateId[] = $value['entity_id'];
		}
		$parameterData = $this->getRequest()->getParams('entity_id');
		$selectedAppsid = $this->getRequest()->getParams('entity_id');
		if (array_key_exists("selected", $parameterData)) {
			$selectedAppsid = $parameterData['selected'];
		}
		if (array_key_exists("excluded", $parameterData)) {
			if ($parameterData['excluded'] == 'false') {
				$selectedAppsid = $templateId;
			} else {
				$selectedAppsid = array_diff($templateId, $parameterData['excluded']);
			}
		}
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter('entity_id', ['in' => $selectedAppsid])->addFieldToSelect('*');
		$model = [];
		foreach ($collection as $item) {
			$newUrlKey = $this->checkUrlKey($item->getUrlKey());
			$products = $this->modelProductTechProduct->create()->getCollection()->addFieldToFilter("producttechnology_id", ["eq" => $item->getEntityId()]);
			$duplicateData = $item->getData();
			$duplicateData["url_key"] = $newUrlKey;
			array_shift($duplicateData);
			$this->createDuplicate($duplicateData, $products->getData());
			$this->messageManager->addSuccess(__('Duplicate generated'));
		}
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
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
		$model = $this->modelProductTech->create()->getCollection()->addFieldToFilter("url_key", ["like" => "%" . $urlKey . "%"])->setOrder("entity_id", "DESC")->getFirstItem();
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
			$this->messageManager->addError('Product Id not Save.');
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
