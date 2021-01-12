<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Add extends Action {

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $_resultJsonFactory;

	/**
	 * @var \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory
	 */
	protected $templateTextFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context                                  $context             [description]
	 * @param \Magento\Framework\Controller\Result\JsonFactory                     $resultJsonFactory   [description]
	 * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templateTextFactory [description]
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templateTextFactory
	) {
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->templateTextFactory = $templateTextFactory;
		parent::__construct($context);
	}

	/**
	 * Index action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute() {
		$result = $this->_resultJsonFactory->create();
		$template_1 = $this->getRequest()->getParam('template_1');
		$template_2 = $this->getRequest()->getParam('template_2');
		$template_3 = $this->getRequest()->getParam('template_3');
		$template_4 = $this->getRequest()->getParam('template_4');
		$store_id = $this->getRequest()->getParam('store', null);
		if ($store_id == null) {
			$store_id = 0;
		}
		$html = '';
		$template_no = 0;
		$templateColl = $this->templateTextFactory->create();
		if ($template_1) {
			$templateColl->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $template_1]);
			$template_no = 1;
			foreach ($templateColl as $key => $value) {
				$html .= $value->getTemplateTextComment();
			}
		}
		if ($template_2) {
			$templateColl->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $template_2]);
			$template_no = 2;
			foreach ($templateColl as $key => $value) {
				$html .= $value->getTemplateTextComment();
			}
		}
		if ($template_3) {
			$templateColl->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $template_3]);
			$template_no = 3;
			foreach ($templateColl as $key => $value) {
				$html .= $value->getTemplateTextComment();
			}
		}
		if ($template_4) {
			$templateColl->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $template_4]);
			$template_no = 4;
			foreach ($templateColl as $key => $value) {
				$html .= $value->getTemplateTextComment();
			}
		}
		return $result->setData(['template_content' => $html, 'template_num' => $template_no]);
	}

	protected function _isAllowed() {
		return true;
	}
}