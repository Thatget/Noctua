<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */

namespace SIT\Mainpagevideo\Block\Mainpagevideo;

use Magento\Framework\View\Element\Template\Context;

class Mainpagevideo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SIT\Mainpagevideo\Model\ResourceModel\Mainpagevideo\CollectionFactory
     */
    protected $_videocollFactory;
    /**
     * @var string
     */
    protected $countryCode;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \SIT\Mainpagevideo\Model\ResourceModel\Mainpagevideo\CollectionFactory $videocollFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        array $data = []
    ) {
        $this->_videocollFactory = $videocollFactory;
        $this->_storeManager = $storeManager;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        if (isset($_SERVER['GEOIP_COUNTRY_CODE'])) {
            $this->countryCode = strtolower($_SERVER['GEOIP_COUNTRY_CODE']);
        }
        parent::__construct($context, $data);
    }

    /**
     * Collection of main page video
     *
     * @return \SIT\Mainpagevideo\Model\ResourceModel\Mainpagevideo\CollectionFactory
     */
    public function getMainVideoData()
    {
        $currentStore = $this->_storeManager->getStore()->getId();
        $mainVideoData = $this->_videocollFactory->create();
        $videoColl = $mainVideoData->setStoreId($currentStore)->addAttributeToSelect('*')->addAttributeToFilter('status', ['eq' => 1])->setOrder('position', 'asc');
        return $videoColl;
    }
    public function getDataCollectionArray()
    {
        $collection = $this->getMainVideoData();
        $result = [];
        foreach($collection as $item)
        {
            if($imageUrl = $item->getData('merchendise_image'))
            {
                $item->setData('merchendise_image', $this->getMediaUrl() . 'merchendise/image/' .$imageUrl);
            }else{
                $item->setData('merchendise_image', null);
            }
            if(!$item->getData('new_label'))
            {
                $item->setData('new_label', null);
            }
            $item->setData('main_video_url', $this->getVideoLink($item));
            $result[] = $item->getData();
        }
        return $result;
    }
    public function getCollectionJson()
    {
        return $this->serializer->serialize($this->getDataCollectionArray());
    }
    public function getVideoLink($value)
    {
        // if($value->getVideoLinkCn() && ((!$this->countryCode && $this->getCurrentCountryCode() == 'cn' ) || $this->countryCode == 'cn') )
        if ($value->getData('main_video_url_cn') && $this->countryCode == 'cn') {
            return $value->getData('main_video_url_cn');
        }
        return $value->getData('main_video_url');
    }
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
