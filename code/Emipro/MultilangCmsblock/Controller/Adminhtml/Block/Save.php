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

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\Controller\Adminhtml\Dashboard
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Cms\Model\Block $block,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        BlockFactory $blockFactory = null,
        BlockRepositoryInterface $blockRepository = null,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
        $this->block = $block;
        $this->session = $session;
        $this->urlInterface = $urlInterface;
        $this->resultPageFactory = $resultPageFactory;
        $this->blockFactory = $blockFactory
        ?: \Magento\Framework\App\ObjectManager::getInstance()->get(BlockFactory::class);
        $this->blockRepository = $blockRepository
        ?: \Magento\Framework\App\ObjectManager::getInstance()->get(BlockRepositoryInterface::class);
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $delStore = false;
        $newblockid = "";
        $oldblockid = "";

        $connection = $this->resourceConnection->getConnection('default');

        $urlInterface = $this->urlInterface;
        $url = $urlInterface->getCurrentUrl();
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

        //for EDIT block save START
        $tableStore = $connection->getTableName("cms_block_store");
        $tableBlockTrack = $connection->getTableName("cms_block_track");
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $data['store_id'][0] = $data['block_store'];
            unset($data['block_store']);
            $model = $this->block;
            $store = $model->getCollection();
            $id = $this->getRequest()->getParam('block_id');

            if ($id) {
                $query = 'SELECT * FROM ' . $tableStore . ' where block_id=' . $id;
                $results = $connection->fetchAll($query);
                if (count($results) > 1) {
                    $delStore = true;
                }
                if ($data["store_id"][0] != "") {
                    $store->getSelect()
                        ->join(["store" => $tableStore], 'main_table.block_id=store.block_id')
                        ->where("store.store_id=" . $data['store_id'][0] . " and main_table.block_id=" . $data["block_id"]);
                    //new block for specifc store
                    if ((($delStore) && count($store->getData()) > 0) || count($store->getData()) <= 0) {
                        unset($data["form_key"]);
                        unset($data["block_id"]);
                        $model->setdata($data);
                        $oldblockid = $data["old_block_id"];
                    }
                    //edit block
                    else {
                        $blockid = $data["block_id"];
                        $model->load($blockid);
                        $model->setData($data);
                        $model->setId($blockid);
                        $oldblockid = $blockid;
                    }
                }
            } else {
                //add
                if (empty($data["store_id"][0])) {
                    $data["store_id"][0] = 0;
                }
                $model->setData($data);
            }
            //for EDIT block save END

            //for new block save START
            try
            {

                if ($delStore) {
                    $connection->query("delete from " . $tableStore . " where block_id=" . $oldblockid . " and store_id=" . $data["store_id"][0]);
                    $connection->query("delete from " . $tableBlockTrack . " where new_block_id=" . $oldblockid . " and store_id=" . $data["store_id"][0]);
                }
                $model->save();
                if (empty($data["block_id"])) {

                    if ($oldblockid == "") {
                        $oldblockid = $model->getId();
                    }
                    $insQuery = "INSERT INTO " . $tableBlockTrack . "(new_block_id,old_block_id,store_id) VALUES (" . $model->getId() . "," . $oldblockid . "," . $data["store_id"][0] . ")";
                    $connection->query($insQuery);

                }
                $this->messageManager->addSuccess(__('You saved the block.'));

                if ($this->getRequest()->getParam('back') == 'duplicate') {
                    return $this->processBlockReturn($model, $data, $resultRedirect, $tableBlockTrack, $connection);
                }

                $this->session->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['block_id' => $model->getId(), "store" => $data["store_id"][0]]);
                }
                return $resultRedirect->setPath('*/*/');

            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                // $this->messageManager->addException($e, __('Something went wrong while saving the page.'));
            }
            //for new block save END
            $this->session->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['block_id' => $this->getRequest()->getParam('block_id'), 'store' => $this->getRequest()->getParam('store', 0)]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function processBlockReturn($model, $data, $resultRedirect, $tableBlockTrack, $connection)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', ['block_id' => $model->getId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } else if ($redirect === 'duplicate') {
            $duplicateModel = $this->blockFactory->create(['data' => $data]);
            $duplicateModel->setId(null);

            /*$identifier = $this->checkIdentifier($data['identifier']);
            $duplicateModel->setIdentifier($identifier);*/
            $duplicateModel->setIdentifier($data['identifier'] . '-' . uniqid());
            $duplicateModel->setIsActive(Block::STATUS_DISABLED);
            $this->blockRepository->save($duplicateModel);
            $id = $duplicateModel->getId();
            $insQuery = "INSERT INTO " . $tableBlockTrack . "(new_block_id,old_block_id,store_id) VALUES (" . $id . "," . $id . "," . $data["store_id"][0] . ")";
            $connection->query($insQuery);
            $this->messageManager->addSuccessMessage(__('You duplicated the block.'));
            $this->dataPersistor->set('cms_block', $data);
            $resultRedirect->setPath('*/*/edit', ['block_id' => $id]);
        }
        return $resultRedirect;
    }

    private function checkIdentifier($urlKey)
    {

        return $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
        ? $matches[1] . '-' . ($matches[2] + 1)
        : $urlKey . '-1';
    }

}
