<?php
namespace Onibi\StoreLocator\Controller\Storelocator;

use Magento\Framework\Controller\ResultFactory;

class Wheretobuy extends \Magento\Framework\App\Action\Action {
	/**
	 * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
	 */
	protected $countryCollectionFactory;

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @var \Onibi\StoreLocator\Model\StoreFactory
	 */
	protected $storeFactory;

	/**
	 * @var \Magento\Directory\Model\Country
	 */
	protected $country;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param \Magento\Framework\App\Action\Context                            $context                  [description]
	 * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory [description]
	 * @param \Magento\Framework\Json\Helper\Data                              $jsonHelper               [description]
	 * @param \Onibi\StoreLocator\Model\StoreFactory                           $storeFactory             [description]
	 * @param \Magento\Directory\Model\Country                                 $country                  [description]
	 * @param \Magento\Framework\View\Result\PageFactory                       $resultPageFactory        [description]
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Onibi\StoreLocator\Model\StoreFactory $storeFactory,
		\Magento\Directory\Model\Country $country,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->countryCollectionFactory = $countryCollectionFactory;
		$this->jsonHelper = $jsonHelper;
		$this->storeFactory = $storeFactory;
		$this->country = $country;
		parent::__construct($context);
	}

	/**
	 * @return json response
	 */
	public function execute() {
		$resultPage = $this->resultPageFactory->create();
		if ($this->getRequest()->getContent()) {
			$paramArray = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
			if (array_key_exists('countryList', $paramArray)) {
				return $this->getCountries();
			}
			if (array_key_exists('regionsList', $paramArray)) {
				return $this->getRegion();
			}
			if (array_key_exists('buyerSellerList', $paramArray)) {
				return $this->getBuyerSellerData();
			}
		}
	}

	/**
	 * Buy/Sell noctua provinces dropdown collection
	 * @return json response
	 */
	public function getRegion() {
		$province = $this->storeFactory->create()->getCollection()
			->addFieldToSelect(['country_id', 'province'])
			->addFieldToFilter('status', ['eq' => 1])
			->addFieldToFilter('province', ['neq' => ''])
			->addFieldToFilter('province', ['neq' => null])
			->addFieldToFilter('province', ['neq' => '-1']);
		$province->getSelect()->group('province');

		$prov_ids = [];
		foreach ($province as $key => $value) {
			$prov_ids[] = $value['province'];
		}
		$regionCollection = $this->country->getRegions();
		$regions = $regionCollection->loadData();
		$count = 0;
		$new_province_array = [];

		foreach ($regions as $key => $value) {
				$new_province_array[$count]['region_code'] = $value['code'];
				$new_province_array[$count]['code'] = $value['country_id'];
				$new_province_array[$count]['default_name'] = $value['default_name'];
            $count++;
		}
		$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
		$response->setHeader('Content-type', 'application/json');
		$response->setContents($this->jsonHelper->jsonEncode($new_province_array));
		return $response;
	}

	/**
	 * Buy/Sell noctua products dropdown collection
	 * @return json response
	 */
	public function getCountries() {
		$collection = $this->storeFactory->create()->getCollection()
            ->addFieldToSelect(['country_id'])
            ->addFieldToFilter('status', ['eq' => 1]);
		$collection->getSelect()->group('country_id');

		$country_ids = [];
		foreach ($collection as $key => $value) {
			$country_ids[] = $value['country_id'];
		}

		$options = $this->getCountryCollection()->toOptionArray();
		$new_country_array = [];
		$count = 0;
		foreach ($options as $key => $value) {
			if (in_array($value['value'], $country_ids)) {
				$new_country_array[$count]['value'] = $value['value'];
				$new_country_array[$count]['label'] = $value['label'];
				$count++;
			}
		}
		$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
		$response->setHeader('Content-type', 'application/json');
		$response->setContents($this->jsonHelper->jsonEncode($new_country_array));
		return $response;
	}

	/**
	 * Get country collection
	 * @return \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
	 */
	public function getCountryCollection() {
		$collection = $this->countryCollectionFactory->create()->loadByStore();
		return $collection;
	}

	/**
	 * Get Buyer/Seller Data
	 * @return \Onibi\StoreLocator\Model\StoreFactory
	 */
	public function getBuyerSellerData() {
		$options = $this->getCountryCollection()->toOptionArray();
		$regionCollection = $this->country->getRegions();
		$regions = $regionCollection->loadData();
		$countryData = [];
		foreach ($options as $key => $value) {
			$countryData[$value['value']] = $value['label'];
		}
		$regionsData = [];
		$extraRegionsData = [];
		foreach ($regionCollection->getData() as $key => $value) {
			$regionsData[$value['country_id']][$value['code']] = $value['name'];
            $extraRegionsData[$value['country_id']][$value['region_id']] = $value['code'];
		}
		$data = [];

		$collection = $this->storeFactory->create()->getCollection()->addFieldToFilter('status', ['eq' => 1])->setOrder('name', 'ASC');
		/**
		 * Add contry name & region name for display it in front-end
		 */
		foreach ($collection->getData() as $key => $value) {

			if (array_key_exists($value['country_id'], $countryData)) {
				$value["country_name"] = $countryData[$value['country_id']];
				if (array_key_exists($value['country_id'], $regionsData)) {
					if (array_key_exists($value["province"], $extraRegionsData[$value["country_id"]])) {
						$value["province"] = $extraRegionsData[$value["country_id"]][$value["province"]];
						$value["region_name"] = $regionsData[$value["country_id"]][$value["province"]];
					} else {
						$value["region_name"] = '';
					}
				} else {
					$value["region_name"] = '';
				}
			} else {
				$value["country_name"] = '';
				$value["region_name"] = '';
			}
			array_push($data, $value);
		}
		$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
		$response->setHeader('Content-type', 'application/json');
		$response->setContents($this->jsonHelper->jsonEncode($data));
		return $response;
	}
}
