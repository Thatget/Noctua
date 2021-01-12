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
namespace Emipro\MultilangCmsblock\Model;
use Magento\Framework\Model\AbstractModel;
class Blocktrack extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Emipro\MultilangCmsblock\Model\ResourceModel\Blocktrack');
    }
}
