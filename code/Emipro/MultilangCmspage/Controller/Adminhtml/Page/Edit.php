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
namespace Emipro\MultilangCmspage\Controller\Adminhtml\Page;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Cms::save';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    protected $resultPage;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_sessionManager;
    protected $_page;
    protected $session;
    protected $resourceConnection;
    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Cms\Model\Page $page,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\Registry $registry
    ) {
        $this->_sessionManager = $sessionManager;
        $this->_page = $page;
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceConnection = $resourceConnection;
        $this->session = $session;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Cms::cms_page')
            ->addBreadcrumb(__('CMS'), __('CMS'))
            ->addBreadcrumb(__('Manage Pages'), __('Manage Pages'));
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        // Get ID and create model
        $id = $this->getRequest()->getParam('page_id');
        $pid = [];

        $connection = $this->resourceConnection->getConnection('default');
        $tableStore = $connection->getTableName('cms_page_store');
        $tablePageTrack = $connection->getTableName('cms_page_track');
        $model = $this->_page;
        $store = $model->getCollection();
        // Initial checking
        $page_identifier = $pageIdentifier = $this->_page->load($id)->getIdentifier();
        if ($this->_sessionManager->getPageIdentifier()) {
            $this->_sessionManager->unsPageIndentifier();
        }
        $this->_sessionManager->setPageIdentifier($page_identifier);
        $store_id = $this->getRequest()->getParam("store");
        if (empty($store_id)) {
            $store_id = 0;
        }
        if ($id) {
            $collection = $this->_page->getCollection();
            $q = $collection->getSelect()->join(["store" => $tableStore], "main_table.page_id=store.page_id")->where("main_table.page_id in (select new_page_id from $tablePageTrack where old_page_id in (select old_page_id from $tablePageTrack where new_page_id=$id) and (store_id=" . $store_id . "))");

            $collection->setOrder('main_table.page_id', "DESC")->setPageSize(1);
            $pid = $collection->getData();
            if (count($pid) < 1) {
                $collection1 = $this->_page->getCollection();
                $collection1->getSelect()
                    ->join(["store" => $tableStore], "main_table.page_id=store.page_id")
                    ->where("main_table.page_id in (select new_page_id from $tablePageTrack where old_page_id in (select old_page_id from $tablePageTrack where new_page_id=$id) and (store_id=0))");
                $collection1->setOrder('main_table.page_id', "DESC")->setPageSize(1);
                $pid = $collection1->getData();
            }
        }
        if (count($pid) > 0) {
            $id = $pid[0]["page_id"];
        }
        $model = $this->_page;
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This page no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('cms_page', $model);
        //Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Pages'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Page'));
        return $resultPage;
    }
}
