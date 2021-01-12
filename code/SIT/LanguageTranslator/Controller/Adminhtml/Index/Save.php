<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\LanguageTranslator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use SIT\LanguageTranslator\Model\Tranlation;

class Save extends \Magento\Backend\App\Action {
	protected $LanguageTranslatormodel;
	protected $adminsession;

	public function __construct(
		Action\Context $context,
		Tranlation $LanguageTranslatormodel,
		Session $adminsession,
		\Magento\Framework\UrlInterface $url,
		\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
		\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
	) {
		parent::__construct($context);
		$this->LanguageTranslatormodel = $LanguageTranslatormodel;
		$this->url = $url;
		$this->adminsession = $adminsession;
		$this->_cacheTypeList = $cacheTypeList;
		$this->_cacheFrontendPool = $cacheFrontendPool;
	}

	public function execute() {
		$data = $this->getRequest()->getPostValue();

		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data) {
			$translation_id = $this->getRequest()->getParam('translation_id');
			if ($translation_id) {
				$this->LanguageTranslatormodel->load($translation_id);
			}

			$this->LanguageTranslatormodel->setData($data);

			try {
				$this->LanguageTranslatormodel->save();
				$this->messageManager->addSuccess(__('The data has been saved.'));
				$types = array('translate');
				foreach ($types as $type) {
					$this->_cacheTypeList->invalidate($type);
				}
				$this->adminsession->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					if ($this->getRequest()->getParam('back') == 'add') {
						return $resultRedirect->setPath('*/*/add');
					} else {
						return $resultRedirect->setPath(
							'*/*/edit',
							[
								'translation_id' => $this->LanguageTranslatormodel->getTranslationId(),
								'_current' => true,
							]
						);
					}
				}
				return $resultRedirect->setPath('*/*/');
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the data.'));
			}

			$this->_getSession()->setFormData($data);
			return $resultRedirect->setPath('*/*/edit', ['translation_id' => $this->getRequest()->getParam('translation_id')]);
		}
		return $resultRedirect->setPath('*/*/');
	}
}
