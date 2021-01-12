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
class FullAction implements OptionSourceInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    protected $_helper;

    /**
     * Constructor
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
    \Qsoft\ContentCustom\Helper\Data $helper
    )
    {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        $options[] = ['label' => 'Select Page', 'value' => ''];

        foreach ($this->_helper->getFullActions() as $value) {
            $options[] = [
                'label' => $value,
                'value' => $value
            ];
        }
        return $options;
    }
}
