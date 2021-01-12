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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Backend\Controller\Adminhtml\Dashboard
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $block;
    protected $resourceConnection;
    protected $urlInterface;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(

        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Cms\Model\Block $block,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\UrlInterface $urlInterface
    ) {

        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->block = $block;
        $this->resourceConnection = $resourceConnection;
        $this->urlInterface = $urlInterface;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // check if we know what should be deleted
        $resultRedirect = $this->resultRedirectFactory->create();
        $urlInterface = $this->urlInterface;
        $url = $urlInterface->getCurrentUrl();
        $pieces = explode("/", $url);
        $len = count($pieces);
        $i = 0;
        $storeId = "";
        for ($e = 0; $e < $len; $e++) {
            if ($pieces[$e] == "store") {
                $i = 1;
            }
            if ($i == 1) {
                $r = $e;
                $storeId = $pieces[$r + 1];
                break;
            }
        }
        $store = $storeId;
        if ($id = $this->getRequest()->getParam('block_id')) {
            $title = "";
            try
            {
                $connection1 = $this->resourceConnection->getConnection('default');
                $model = $this->block;
                $model->load($id);
                $model->delete();
                $results = $connection1->query("delete from cms_block_track where new_block_id=" . $id . " and store_id=" . $store . "");
                // display success message
                $this->messageManager->addSuccess(__('You deleted the block.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', ['block_id' => $id]);
                return;
            }
            $this->messageManager->addError(__('Unable to find a block to delete.'));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
