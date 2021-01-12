<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_MultilangCmspage
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
namespace Emipro\MultilangCmspage\Model\ResourceModel\Pagetrack;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection{
    protected function _construct()
    {
        $this->_init('Emipro\MultilangCmspage\Model\Pagetrack', 'Emipro\MultilangCmspage\Model\ResourceModel\Pagetrack');
    }
}