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

use Magento\Backend\Block\Widget\Context;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;
    protected $urlInterface;

    /**
     * @param Context $context
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        Context $context,
        PageRepositoryInterface $pageRepository,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->context = $context;
        $this->pageRepository = $pageRepository;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Return CMS page ID
     *
     * @return int|null
     */
    public function getPageId()
    {
        try {
            return $this->pageRepository->getById(
                $this->context->getRequest()->getParam('page_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
