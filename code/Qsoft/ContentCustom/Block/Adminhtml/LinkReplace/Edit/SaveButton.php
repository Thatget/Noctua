<?php
/**
 * Created by PhpStorm.
 * @package OpenTechiz
 * @subpackage Sizer
 * @author trungpham
 * @copyright OpenTechiz
 * Date: 29/03/2019
 * Time: 18:30
 */

namespace Qsoft\ContentCustom\Block\Adminhtml\LinkReplace\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
