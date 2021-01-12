<?php
/**
 * Created by PhpStorm.
 * @package OpenTechiz
 * @subpackage Sizer
 * @author trungpham
 * @copyright OpenTechiz
 * Date: 01/04/2019
 * Time: 09:18
 */

namespace Qsoft\ContentCustom\Model\LinkReplace\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class StoreList
 */
class StoreList implements OptionSourceInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $storeManagerDataList = $this->_storeManager->getStores();
        $options[] = ['label' => '', 'value' => ''];

        foreach ($storeManagerDataList as $key => $value) {
            $options[] = [
                'label' => $value->getName(),
                'value' => $value->getStoreId()
            ];
        }
        return $options;
    }
}
