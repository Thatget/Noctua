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
use Magento\Cms\Model\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magento_Cms::save';
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    protected $page;
    protected $urlInterface;
    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     */
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor $dataProcessor,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Cms\Model\Page $page,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\UrlInterface $urlInterface,
        DataPersistorInterface $dataPersistor,
        \Magento\Cms\Model\PageFactory $pageFactory = null,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository = null
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->resourceConnection = $resourceConnection;
        $this->page = $page;
        $this->session = $session;
        $this->urlInterface = $urlInterface;
        $this->pageFactory = $pageFactory
        ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Cms\Model\PageFactory::class);
        $this->pageRepository = $pageRepository
        ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Cms\Api\PageRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $delStore = false;
        $data = $this->getRequest()->getPostValue();
        //////// STORE ID   START
        $url = $this->urlInterface->getCurrentUrl();
        $pieces = explode("/", $url);
        $len = count($pieces);
        $i = 0;
        $store_id = 0;
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
        //////// STORE ID END
        $connection = $this->resourceConnection->getConnection('default');
        $newpageid = "";
        $oldpageid = "";
        $tableStore = $connection->getTableName('cms_page_store');
        $tablePage = $connection->getTableName('cms_page');
        $tablePageTrack = $connection->getTableName('cms_page_track');
        $conn = $connection;
        $model = $this->page;
        $store = $model->getCollection();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $data['store_id'][0] = $data['page_store'];
            unset($data['page_store']);
            $data = $this->dataProcessor->filter($data);
            $id = $this->getRequest()->getParam('page_id');
            if ($id) {
                $query = 'SELECT * FROM ' . $tableStore . ' where page_id=' . $id;
//                 echo $query;

                $results = $connection->fetchAll($query);
// 				echo count($results) . '---444';
                if (count($results) > 1) {
                    $delStore = true;
                }
                if ($data["store_id"][0] != "") {
                    $ttt = $store->getSelect()
                        ->join(["store" => $tableStore], 'main_table.page_id=store.page_id')
                        ->where("store.store_id=" . $data['store_id'][0] . " and main_table.page_id=" . $data["page_id"]);
                    //new page for specifc store
                    // var_dump($store->getData());
                    
                    $sQuery = 'SELECT `main_table`.*, `store`.* FROM ' . $tablePage . ' AS `main_table` INNER JOIN ' . $tableStore . ' AS `store` ON main_table.page_id=store.page_id WHERE (store.store_id=' . $data['store_id'][0] . ' and main_table.page_id=' . $data["page_id"] . ')';
                    
//                     $store->getSelect()->__toString();// 
// 					echo $store->getSelect();die;
				$sResults = $connection->fetchAll($sQuery);
				echo count($sResults) . '---444';
               
//                     if ((($delStore) && count($store->getData()) > 0) || count($store->getData()) <= 0) {
					if ((($delStore) && count($sResults) > 0) || count($sResults) <= 0) {
// 					if (count($store->getData()) <= 0) {
                        unset($data["form_key"]);
                        unset($data["page_id"]);
                        $model->setdata($data);
                        $oldpageid = $data["old_page_id"];
                        //START remove  store_id , page_id (whole record)  from url_rewrite table To overcome  url already exist error
                        $query = 'delete FROM url_rewrite where entity_id=' . $oldpageid . ' and store_id=' . $data["store_id"][0];
//                         echo  $query; die;
                        $results = $connection->query($query);
                        //END remove  store_id , page_id (whole record)  from url_rewrite table
                    }
                    //edit page
                    else {
                        $pageid = $data["page_id"];
                        $model->load($pageid);
                        $model->setData($data);
                        $model->setId($pageid);
                        $oldpageid = $pageid;
                    }
                }
            } else {
                //add
                if (empty($data["store_id"][0])) {
                    $data["store_id"][0] = 0;
                }
                $model->setData($data);
            }

            try {
                if ($delStore) {
                    $connection->query("delete from  $tableStore where page_id=" . $oldpageid . " and store_id=" . $data["store_id"][0]);
                    $connection->query("delete from  $tablePageTrack where new_page_id=" . $oldpageid . " and store_id=" . $data["store_id"][0]);
                }
                $model->save();
                if (empty($data['page_id'])) {

                    if ($oldpageid == "") {
                        $oldpageid = $model->getId();
                    }
                    $insQuery = "INSERT INTO " . $tablePageTrack . "(new_page_id,old_page_id,store_id) VALUES (" . $model->getId() . "," . $oldpageid . "," . $data["store_id"][0] . ")";
                    $connection->query($insQuery);
                }

                // save the data
                // display success message
                $this->messageManager->addSuccess(__('You saved the page.'));

                if ($this->getRequest()->getParam('back') == 'duplicate') {
                    return $this->processResultRedirect($model, $data, $resultRedirect, $tablePageTrack, $connection);
                }
                // clear previously saved data from session
                $this->session->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['page_id' => $model->getId(), "store" => $data["store_id"][0], '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
//                 $this->messageManager->addError($e->getMessage());
				$this->messageManager->addException($e, __('Something went wrong while saving the page.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the page.'));
            }
            $this->session->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['page_id' => $this->getRequest()->getParam('page_id'), 'store' => $this->getRequest()->getParam('store', 0)]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function processResultRedirect($model, $data, $resultRedirect, $tablePageTrack, $connection)
    {
        $newPage = $this->pageFactory->create(['data' => $data]);
        $newPage->setId(null);
        $identifier = $model->getIdentifier() . '-' . uniqid();
        $newPage->setIdentifier($identifier);
        $newPage->setIsActive(false);
        $this->pageRepository->save($newPage);
        $insQuery = "INSERT INTO " . $tablePageTrack . "(new_page_id,old_page_id,store_id) VALUES (" . $newPage->getId() . "," . $newPage->getId() . "," . $data["store_id"][0] . ")";
        $connection->query($insQuery);
        $this->messageManager->addSuccessMessage(__('You duplicated the page.'));
        return $resultRedirect->setPath(
            '*/*/edit',
            [
                'page_id' => $newPage->getId(),
                '_current' => true,
            ]
        );

    }
}
