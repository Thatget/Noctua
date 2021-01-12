<?php
/**
 * Created by PhpStorm.
 * @package OpenTechiz
 * @subpackage Sizer
 * @author trungpham
 * @copyright OpenTechiz
 * Date: 29/03/2019
 * Time: 17:58
 */

namespace Qsoft\ContentCustom\Block\Adminhtml\LinkReplace\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }

    /**
     * Return the synonyms group Id.
     *
     * @return integer|null
     */
    public function getId()
    {
        $ringsizer = $this->registry->registry('ringsizer');
        return $ringsizer ? $ringsizer->getId() : null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
