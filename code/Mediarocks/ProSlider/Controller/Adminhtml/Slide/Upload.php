<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [22.03.2019]
 */
namespace Mediarocks\ProSlider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Mediarocks\ProSlider\Model\ImageUploader;
use \Magento\Backend\App\Action\Context;

/**
 * Class Upload
 */
class Upload extends Action {

	/**
	 * @var ImageUploader
	 */
	public $imageUploader;

	/**
	 * @param Context       $context       [description]
	 * @param ImageUploader $imageUploader [description]
	 * @param ResultFactory $resultFactory [description]
	 */
	public function __construct(
		Context $context,
		ImageUploader $imageUploader,
		ResultFactory $resultFactory
	) {
		parent::__construct($context);
		$this->resultFactory = $resultFactory;
		$this->imageUploader = $imageUploader;
	}

	/**
	 * Upload file controller action
	 */
	public function execute() {
		try {
			$param = $this->getRequest()->getParam('param_name');
			$result = $this->imageUploader->saveFileToTmpDir($param);
			$result['cookie'] = [
				'name' => $this->_getSession()->getName(),
				'value' => $this->_getSession()->getSessionId(),
				'lifetime' => $this->_getSession()->getCookieLifetime(),
				'path' => $this->_getSession()->getCookiePath(),
				'domain' => $this->_getSession()->getCookieDomain(),
			];
		} catch (\Exception $e) {
			$result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
		}
		return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
	}
}
