<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Ui\Component\Form\Mainboard;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\ResourceModel\ProductCompatibility\Collection;

class DataProvider extends AbstractDataProvider {

	/**
	 * @var Collection
	 */
	protected $collection;

	/**
	 * @var FilterPool
	 */
	protected $filterPool;

	/**
	 * @var array
	 */
	protected $loadedData;

	/**
	 * @var RequestInterface
	 */
	protected $request;

	/**
	 * @var ProductCompHelper
	 */
	protected $prodCompHelper;

	/**
	 * @param string            $name             [description]
	 * @param string            $primaryFieldName [description]
	 * @param string            $requestFieldName [description]
	 * @param Collection        $collection       [description]
	 * @param FilterPool        $filterPool       [description]
	 * @param RequestInterface  $request          [description]
	 * @param ProductCompHelper $prodCompHelper   [description]
	 * @param array             $meta             [description]
	 * @param array             $data             [description]
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		Collection $collection,
		FilterPool $filterPool,
		RequestInterface $request,
		ProductCompHelper $prodCompHelper,
		array $meta = [],
		array $data = []
	) {
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
		$this->collection = $collection;
		$this->filterPool = $filterPool;
		$this->request = $request;
		$this->prodCompHelper = $prodCompHelper;
	}

	/**
	 * Get data
	 *
	 * @return array
	 */
	public function getData() {

		if (!$this->loadedData) {

			$storeId = (int) $this->request->getParam('store', 0);
			$this->collection->setStoreId($storeId)->addAttributeToSelect('*');
			$items = $this->collection->getItems();
			/**
			 * Changed by RH [24-04-2019] : Display data in mainboard form
			 */
			foreach ($items as $item) {
				if ($item['comp_socket'] != "") {
					$socketAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_SOCKET);
					$socketAttrId = $socketAttrInfo->getAttributeId();
					$socketoption = $this->prodCompHelper->getAttrOptionLabel($socketAttrId, $item['comp_socket'])->getFirstItem();
					$item['comp_socket'] = $socketoption['value'];
				}if ($item['comp_manufacture'] != "") {
					$manufAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MANUFACTURE);
					$manufAttrId = $manufAttrInfo->getAttributeId();
					$manufoption = $this->prodCompHelper->getAttrOptionLabel($manufAttrId, $item['comp_manufacture'])->getFirstItem();
					$item['comp_manufacture'] = $manufoption['value'];
				}if ($item['comp_model'] != "") {
					$modelAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_MODEL);
					$modelAttrId = $modelAttrInfo->getAttributeId();
					$modeloption = $this->prodCompHelper->getAttrOptionLabel($modelAttrId, $item['comp_model'])->getFirstItem();
					$item['comp_model'] = $modeloption['value'];
				}if ($item['comp_type'] != "") {
					$compTypeAttrInfo = $this->prodCompHelper->getAttributeInfo(ProductCompHelper::COMP_EAV_TYPE, ProductCompHelper::COMP_TYPE);
					$compTypeAttrId = $compTypeAttrInfo->getAttributeId();
					$compTypeoption = $this->prodCompHelper->getAttrOptionLabel($compTypeAttrId, $item['comp_type'])->getFirstItem();
					$item['comp_type'] = $compTypeoption['value'];
				}
				/**
				 * Changed by RH [24-04-2019] : Display data in mainboard form
				 */
				$item->setStoreId($storeId);
				/**
				 * Code by RH : Store vise comp socket,manuf,model etc field enable/disable
				 */
				if ($storeId && $storeId != 0) {
					$item['do_we_hide_it'] = true;
				} else {
					$item['do_we_hide_it'] = false;
				}
				/**
				 * Code by RH : Store vise comp socket,manuf,model etc field enable/disable
				 */
				$this->loadedData[$item->getEntityId()] = $item->getData();
				break;
			}
		}
		return $this->loadedData;
	}
}
