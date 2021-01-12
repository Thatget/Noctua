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

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Backend\Controller\Adminhtml\Dashboard
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Cms::page_delete';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_resource;
    protected $resourceConnection;
    protected $page;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Cms\Model\Page $page,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {

        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->page = $page;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $store = $this->getRequest()->getParam('store');
        if ($id = $this->getRequest()->getParam('page_id')) {

            try
            {
                $objectManager1 = \Magento\Framework\App\ObjectManager::getInstance();
                $connection1 = $objectManager1->create('Magento\Framework\App\ResourceConnection')->getConnection('default');
                // $connection1 = $resourceConnection->getConnection('default');
                $model = $this->page;

                $model->load($id);
                $model->delete();

                $delQuery = "DELETE FROM cms_page_track where new_page_id=" . $id . " and store_id=" . $store . "";
                $results = $connection1->query($delQuery);

                // display success message
                $this->messageManager->addSuccess(__('You deleted the page.'));
                // go to grid
                return $resultRedirect->setPath('*/*/', ["store" => $store, '_current' => true]);

            } catch (\Exception $e) {
                $this->messageManager->addError(__("There some error"));
                // go back to edit form
                $this->_redirect('*/*/edit', ['page_id' => $id]);
                return;
            }

            $this->messageManager->addError(__('Unable to find a page to delete.'));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
