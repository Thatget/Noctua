<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh
 */

namespace SIT\LanguageTranslator;

use Magento\Framework\App\ObjectManager;
use \Magento\Framework\App\ResourceConnection;

class Translate extends \Magento\Framework\Translate {
	protected $storeManager;
	protected $resource;

	public function __construct(
		\Magento\Framework\View\DesignInterface $viewDesign,
		\Magento\Framework\Cache\FrontendInterface $cache,
		\Magento\Framework\View\FileSystem $viewFileSystem,
		\Magento\Framework\Module\ModuleList $moduleList,
		\Magento\Framework\Module\Dir\Reader $modulesReader,
		\Magento\Framework\App\ScopeResolverInterface $scopeResolver,
		\Magento\Framework\Translate\ResourceInterface $translate,
		\Magento\Framework\Locale\ResolverInterface $locale,
		\Magento\Framework\App\State $appState,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\File\Csv $csvParser,
		\Magento\Framework\App\Language\Dictionary $packDictionary,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		ResourceConnection $resource

	) {
		parent::__construct($viewDesign, $cache, $viewFileSystem, $moduleList, $modulesReader, $scopeResolver, $translate, $locale, $appState, $filesystem, $request, $csvParser, $packDictionary);
		$this->storeManager = $storeManager;
		$this->resource = $resource;
	}

	/**
	 * Loading current translation from DB
	 *
	 * @return $this
	 */
	protected function _loadDbTranslation() {
		/*Custom Development*/
		$frontstore[] = ["1", "3"];
		$backstore[] = ["2", "3"];
		$currentstoreid = $this->storeManager->getStore()->getId();
		$tableOrder = $this->resource->getTableName('sit_storewise_translation');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$translate = $objectManager->create('SIT\LanguageTranslator\Model\ResourceModel\Tranlation\CollectionFactory');
		$collection = $translate->create();
		$area = $this->_appState->getAreaCode();
		$collection->getSelect()->join(array('tr' => $tableOrder), 'tr.translation_id = main_table.translation_id', array('translated_string' => 'tr.translated_string', 'store_id' => 'tr.store_id'));
		$collection->addFieldToFilter('status', 1)->addFieldToFilter('store_id', ['eq' => $currentstoreid]);
		$transtring = [];
		if ($area == 'frontend') {
			$transtring = $collection->addFieldToFilter('interface', ['in' => $frontstore]);
		}
		if ($area == 'adminhtml') {
			$transtring = $collection->addFieldToFilter('interface', ['in' => $backstore]);
		}

		$sting = [];
		foreach ($transtring as $value) {
			$sting[$value->getOriginalString()] = $value->getTranslatedString();
		}
		/*Custom Development end*/
		$data = $this->_translateResource->getTranslationArray(null, $this->getLocale());
		$data = $data + $sting;
		$this->_addData(array_map('htmlspecialchars_decode', $data));
		return $this;
	}

}
