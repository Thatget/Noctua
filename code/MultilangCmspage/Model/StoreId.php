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

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Data\ValueSourceInterface;

class StoreId implements ValueSourceInterface
{
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->urlInterface = $urlInterface;
    }

    public function getValue($name)
    {
        $model = $this->registry->registry('cms_page');
        $urlInterface = $this->urlInterface;
        $url = $urlInterface->getCurrentUrl();
        $pieces = explode("/", $url);
        $len = count($pieces);
        $i = 0;
        $store_id = "";
        for ($e = 0; $e < $len; $e++) {
            if ($pieces[$e] == "store") {
                $i = 1;
            }
            if ($i == 1) {
                $r = $e;
                $store_id = $pieces[$r + 1];
                break;
            }
        }
        if ($i != 1) {
            $store_id = 0;
        }
        return $store_id;
    }

}
