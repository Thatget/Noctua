<?php
/**
 * Copyright © 2015 RokanThemes.com. All rights reserved.

 * @author RokanThemes Team <contact@rokanthemes.com>
 */

namespace Qsoft\ContentCustom\Block\Adminhtml;

/**
 * Admin blog post
 */
class LinkReplace extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Qsoft_ContentCustom';
        $this->_headerText = __('Replace Link');
        $this->_addButtonLabel = __('Add New');
        parent::_construct();
    }
    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/add');
    }
}
