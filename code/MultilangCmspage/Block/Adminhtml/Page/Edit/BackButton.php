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
namespace Emipro\MultilangCmspage\Block\Adminhtml\Page\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /*public function __construct(
    \Magento\Framework\UrlInterface $urlInterface
    ) {
    $this->urlInterface = $urlInterface;
    }*/
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10,
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        /** @var \Magento\Framework\UrlInterface $urlInterface */
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
        if ($store_id != 0) {
            return $this->getUrl('*/*/', ['_current' => true, "store" => $store_id]);
        } else {
            return $this->getUrl('*/*/', ['_current' => true]);
        }
    }
}
