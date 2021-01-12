<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [27-11-2019]
 */

namespace SIT\Mainpagevideo\Ui\Component\Form\Mainpagevideo;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SIT\Mainpagevideo\Model\ResourceModel\Mainpagevideo\Collection;

class DataProvider extends AbstractDataProvider
{
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * [__construct description]
     * @param string                                     $name             [description]
     * @param string                                     $primaryFieldName [description]
     * @param string                                     $requestFieldName [description]
     * @param Collection                                 $collection       [description]
     * @param FilterPool                                 $filterPool       [description]
     * @param RequestInterface                           $request          [description]
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager     [description]
     * @param array                                      $meta             [description]
     * @param array                                      $data             [description]
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        FilterPool $filterPool,
        RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->filterPool = $filterPool;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->loadedData) {
            $storeId = (int) $this->request->getParam('store');
            $this->collection->setStoreId($storeId)->addAttributeToSelect('*');
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $item->setStoreId($storeId);
                $itemData = $item->getData();
                if (isset($itemData['merchendise_image'])) {
                    $imageName = explode('/', $itemData['merchendise_image']);

                    if (array_key_exists(3, $imageName)) {
                        $imageNameData = $imageName[3];
                    } else {
                        $imageNameData = $itemData['merchendise_image'];
                    }

                    $itemData['merchendise_image'] = array(
                        [
                            'name' => $imageNameData,
                            'url' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'merchendise/image' . $itemData['merchendise_image'],
                        ],
                    );
                } else {
                    $itemData['merchendise_image'] = null;
                }
                $this->loadedData[$item->getEntityId()] = $itemData;
                break;
            }
        }
        return $this->loadedData;
    }
}
