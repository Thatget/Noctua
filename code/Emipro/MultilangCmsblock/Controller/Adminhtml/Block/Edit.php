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
namespace Emipro\MultilangCmsblock\Controller\Adminhtml\Block;

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
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_coreRegistry;
    protected $resultPage;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_sessionManager;
    protected $block;
    protected $urlInterface;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Cms\Model\Block $block,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Registry $registry
    ) {
        $this->_sessionManager = $sessionManager;
        $this->block = $block;
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->session = $session;
        $this->urlInterface = $urlInterface;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Cms::cms_block')
            ->addBreadcrumb(__('CMS'), __('CMS'))
            ->addBreadcrumb(__('Manage Blocks'), __('Manage Blocks'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $urlInterface = $this->urlInterface;

        $id = $this->getRequest()->getParam('block_id');
        $pid = [];
        $connection = $this->resourceConnection->getConnection('default');
        $tableStore = $connection->getTableName('cms_block_store');
        $tableBlockTrack = $connection->getTableName('cms_block_track');

        $model = $this->block;
        $store = $model->getCollection();

        $block_identifier = $pageIdentifier = $this->block->load($id)->getIdentifier();

        if ($this->_sessionManager->getBlockIdentifier()) {
            $this->_sessionManager->unsBlockIndentifier();
        }
        $this->_sessionManager->setBlockIdentifier($block_identifier);
        $store_id = $this->getRequest()->getParam("store");

        if (empty($store_id)) {
            $store_id = 0;
        }

        if ($id) {
            $collection = $this->block->getCollection();
            $collection->getSelect()
                ->join(["store" => $tableStore], "main_table.block_id=store.block_id")
                ->where("main_table.block_id in (select new_block_id from $tableBlockTrack where old_block_id in (select old_block_id from $tableBlockTrack where new_block_id=$id) and (store_id=" . $store_id . "))");
            $collection->setOrder('main_table.block_id', "DESC")->setPageSize(1);

            $pid = $collection->getdata();
            if (count($pid) < 1) {
                $collection1 = $this->block->getCollection();
                $collection1->getSelect()
                    ->join(["store" => $tableStore], "main_table.block_id=store.block_id")
                    ->where("main_table.block_id in (select new_block_id from $tableBlockTrack where old_block_id in (select old_block_id from $tableBlockTrack where new_block_id=$id) and (store_id=0))");
                $collection1->setOrder('main_table.block_id', "DESC")->setPageSize(1);
                $pid = $collection1->getData();
            }
        }

        if (count($pid) > 0) {
            $id = $pid[0]["block_id"];
        }

        $model = $this->block;

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('cms_block', $model);
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Blocks'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Block'));
        return $resultPage;
    }
}
