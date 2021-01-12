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
namespace Emipro\MultilangCmspage\Ui\Component\Listing\Column;

use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class PageActions
 */
class PageActions extends Column
{
    /** Url path */
    const CMS_URL_PATH_EDIT = 'cms/page/edit';
    const CMS_URL_PATH_DELETE = 'cms/page/delete';

    /**
     * @var \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var Escaper
     */
    private $escaper;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        $editUrl = self::CMS_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        $this->request = $request;
        $this->storeManagerInterface = $storeManagerInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        $storeId = $this->request->getParam('store');
        if ($storeId == null) {
            $storeId = 0;
        }

        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['page_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $this->editUrl,
                            [
                                'page_id' => $item['page_id'],
                                'store' => isset($item['_first_store_id']) ? $item['_first_store_id'] : 0,
                            ]
                        ),
                        'label' => __('Edit'),
                    ];
                    $title = $this->getEscaper()->escapeHtml($item['title']);
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::CMS_URL_PATH_DELETE,
                            [
                                'page_id' => $item['page_id'],
                                'store' => isset($item['_first_store_id']) ? $item['_first_store_id'] : 0,
                            ]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $title),
                            'message' => __('Are you sure you want to delete a %1 record?', $title),
                        ],
                        'post' => true,
                    ];
                }
                if (isset($item['identifier'])) {
                    $storeManager = $this->storeManagerInterface;

                    $storeCode = null;
                    $stores = $storeManager->getStores(true, false);
                    foreach ($stores as $store) {
                        if ($store->getID() == $storeId) {
                            $storeCode = $store->getCode();
                        }
                    }
                    $item[$name]['preview'] = [
                        'href' => $this->actionUrlBuilder->getUrl(
                            $item['identifier'],
                            isset($storeId) ? $storeId : null,
                            isset($storeCode) ? $storeCode : null
                        ),
                        'label' => __('View'),
                    ];

                }
            }
        }

        return $dataSource;
    }

    /**
     * Get instance of escaper
     *
     * @return Escaper
     * @deprecated 101.0.7
     */
    private function getEscaper()
    {
        if (!$this->escaper) {
            $this->escaper = ObjectManager::getInstance()->get(Escaper::class);
        }
        return $this->escaper;
    }
}
