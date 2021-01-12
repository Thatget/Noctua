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
namespace Emipro\MultilangCmspage\Model;
use Magento\Framework\Model\AbstractModel;
class Pagetrack extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Emipro\MultilangCmspage\Model\ResourceModel\Pagetrack');
    }
}