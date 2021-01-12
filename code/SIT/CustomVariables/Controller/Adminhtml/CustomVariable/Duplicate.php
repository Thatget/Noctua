<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh
 */
namespace SIT\CustomVariables\Controller\Adminhtml\CustomVariable;

use Magento\Backend\App\Action;
use \Magento\Store\Model\StoreRepository;

class Duplicate extends Action {
	/**
	 * @var \Magento\Variable\Model\VariableFactory
	 */
	protected $varFactory;

	/**
	 * @var StoreRepository
	 */
	protected $storeRepository;

	/**
	 * [__construct description]
	 * @param Action\Context                            $context            [description]
	 * @param \Magento\Framework\App\ResourceConnection $resourceConnection [description]
	 * @param \Magento\Variable\Model\VariableFactory   $varFactory         [description]
	 * @param StoreRepository                           $storeRepository    [description]
	 */
	public function __construct(
		Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Variable\Model\VariableFactory $varFactory,
		StoreRepository $storeRepository
	) {
		parent::__construct($context);
		$this->resourceConnection = $resourceConnection;
		$this->varFactory = $varFactory;
		$this->storeRepository = $storeRepository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		$varId = $this->getRequest()->getParam('variable_id');
		$editDuplicate = $this->getRequest()->getParam('edit_duplicate');
		$store_id = $this->getRequest()->getParam('store');
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($varId) {
			$model = $this->varFactory->create()->load($varId)->getData();
			try {
				$var_data = $this->varFactory->create();
				$model["code"] = $model["code"] . "_copy";
				$var_data->setData($model)->setVariableId(null);
				$var_data->save();
				$tvar_data = $this->varFactory->create();
				$stores = $this->storeRepository->getList();

				foreach ($stores as $store) {
					$tvar = $this->varFactory->create()->setStoreId($store['store_id'])->load($varId);
					$tvar_data = $tvar_data->setStoreId($store['store_id'])->load($var_data->getVariableId());

					if ($tvar->getData('code')) {
						$tvar_data->setData('code', $tvar->getData('code') . "_copy");
					}

					if ($tvar->getData('name')) {
						$tvar_data->setData('name', $tvar->getData('name'));
					}
					if ($store['store_id'] == 0) {
						if ($tvar->getData('plain_value')) {
							$tvar_data->setData('plain_value', $tvar->getData('plain_value'));
						}
					} else {
						if ($tvar_data->getData('plain_value') != $tvar->getData('plain_value')) {
							$tvar_data->setData('plain_value', $tvar->getData('plain_value'));
						} else {
							$tvar_data->setData('plain_value', false);
						}
					}
					if ($store['store_id'] == 0) {
						if ($tvar->getData('html_value')) {
							$tvar_data->setData('html_value', $tvar->getData('html_value'));
						}
					} else {
						if ($tvar_data->getData('html_value') != $tvar->getData('html_value')) {
							$tvar_data->setData('html_value', $tvar->getData('html_value'));
						} else {
							$tvar_data->setData('html_value', false);
						}
					}

					$tvar_data->setVariableId($var_data->getVariableId())->save();
				}

				$this->messageManager->addSuccess(__('The custom variable has been duplicated.'));
				if ($editDuplicate == 1) {
					$lastId = $this->varFactory->create()->getCollection()->getLastItem();
					return $resultRedirect->setPath('adminhtml/system_variable/edit', ['variable_id' => $lastId->getVariableId()]);
				} else {
					return $resultRedirect->setPath('adminhtml/system_variable/index');
				}
			} catch (\Exception $e) {
				$this->messageManager->addError(__('Variable Code is already coppied!.'));
				return $resultRedirect->setPath('adminhtml/system_variable/index');
			}
		}
		return $resultRedirect->setPath('adminhtml/system_variable/index');
	}
}
