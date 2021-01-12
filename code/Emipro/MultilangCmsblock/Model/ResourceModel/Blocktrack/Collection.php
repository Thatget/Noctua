<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_MultilangCmsblock
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
namespace Emipro\MultilangCmsblock\Model\ResourceModel\Blocktrack;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection{
    protected function _construct()
    {
        $this->_init('Emipro\MultilangCmsblock\Model\Blocktrack', 'Emipro\MultilangCmsblock\Model\ResourceModel\Blocktrack');
    }
}