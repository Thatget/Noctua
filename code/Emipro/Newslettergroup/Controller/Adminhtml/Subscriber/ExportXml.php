<?php

namespace Emipro\Newslettergroup\Controller\Adminhtml\Subscriber;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportXml extends \Magento\Newsletter\Controller\Adminhtml\Subscriber\ExportXml {

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    protected $websiteRepository;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\App\Action\Context $context                           $context             [description]
     * @param \Magento\Eav\Model\Config                                              $eavConfig           [description]
     * @param \Magento\Store\Model\System\Store                                      $systemStore         [description]
     * @param \Magento\Store\Model\StoreManagerInterface                             $storeManager        [description]
     * @param \Magento\Customer\Model\Customer                                       $customer            [description]
     * @param \Magento\Store\Api\WebsiteRepositoryInterface                          $websiteRepository   [description]
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory   $collectionFactory   [description]
     * @param \Magento\Framework\App\Response\Http\FileFactory                       $fileFactory         [description]
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    )
    {
        $this->eavConfig = $eavConfig;
        $this->systemStore = $systemStore;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->websiteRepository = $websiteRepository;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $fileFactory);
    }

    /**
     * generate csv file
     *
     * @return csv file
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['internal_subscriber'])) {
            $this->_view->loadLayout();
            $fileName = 'subscribers.xml';
            $exportBlock = $this->collectionFactory->create();
            $exportBlock->addFieldToFilter('subscriber_id', ['in' => explode(",", $params['internal_subscriber'])])
                ->addFieldToSelect('*')
                ->getSelect()->join(
                    ['store' => $exportBlock->getResource()->getTable('store')],
                    'store.store_id = main_table.store_id',
                    ['group_id', 'website_id']
                )
                ->joinLeft(
                    [
                        'customer' => $exportBlock->getResource()->getTable('customer_entity')
                    ],
                    'main_table.customer_id = customer.entity_id',
                    ['firstname', 'lastname']
                );
            //add type data !
            $exportBlock->addSubscriberTypeField();
            $attribute = $this->eavConfig->getAttribute('catalog_product', 'newsletter_website');
            $csvData = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"><Worksheet ss:Name="subscribers.xml">';
            $csvData .= '<Table><Row><Cell><Data ss:Type="String">ID</Data></Cell>'
                .'<Cell><Data ss:Type="String">Email</Data></Cell><Cell><Data ss:Type="String">Type</Data></Cell>'
                .'<Cell><Data ss:Type="String">Customer First Name</Data></Cell><Cell><Data ss:Type="String">Customer Last Name</Data></Cell>'
                .'<Cell><Data ss:Type="String">Status</Data></Cell><Cell><Data ss:Type="String">Is Bounced</Data></Cell>'
                .'<Cell><Data ss:Type="String">Source Website</Data></Cell><Cell><Data ss:Type="String">Web Site</Data></Cell>'
                .'<Cell><Data ss:Type="String">Store</Data></Cell><Cell><Data ss:Type="String">Store View</Data></Cell></Row>';

            foreach ($exportBlock as $key => $value) {
                $item = $value->getData();
                /*
                 * Get subcriber Type;
                 */
                if ($item['type'] == "2") {
                    $item['type'] = "Customer";
                } else $item["type"] = "Guest";
                /*
                 * Get Subcriber First,Last Name;
                 */
                if (!isset($item['firstname'])) {
                    $item['firstname'] = '"----"';
                }
                if (!isset($item['lastname'])) {
                    $item['lastname'] = '"----"';
                }
                /*
                 * Get Subcriber Status;
                 */
                if ($item['subscriber_status'] == "1") {
                    $item['subscriber_status'] = '"Subscribed"';
                } elseif ($item['subscriber_status'] == "2") {
                    $item["subscriber_status"] = '"Not Activated"';
                } elseif ($item['subscriber_status' == "3"]) {
                    $item["subscriber_status"] = '"Unsubscribed"';
                } else {
                    $item["subscriber_status"] = '"Unconfirmed"';
                }
                /**
                 * Check is_bounced;
                 */
                if ($item['is_bounced'] == "0") {
                    $item['is_bounced'] = "No";
                } else $item['is_bounced'] = "Yes";
                /*
                 * Get Source Website;
                 */
                $item['source_website'] = $attribute->getSource()->getOptionText($item['source_website']);
                /*
                 * Get Website Name;
                 */
                $item['website_name'] = '"' . $this->getSourceWebsite($item['website_id']) . '"';
                /*
                 * Get Store Name;
                 */
                $item['store_name'] = $this->systemStore->getGroupName($item['group_id']);
                /*
                 * Get Store View Name;
                 */
                $item['store_view'] = $this->systemStore->getStoreName($item['store_id']);

                $csvData .= '<Row><Cell><Data ss:Type="Number">'.$item['subscriber_id']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['subscriber_email']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['type']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['firstname']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['lastname']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['subscriber_status']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['is_bounced']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['source_website']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['website_name']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['store_name']
                    .'</Data></Cell><Cell><Data ss:Type="String">' . $item['store_view']
                    .'</Data></Cell></Row>';
            }
            $csvData .= '</Table></Worksheet></Workbook>';
            return $this->_fileFactory->create($fileName, $csvData, DirectoryList::VAR_DIR);
        }
        return parent::execute();
    }
    /**
     * @param string $websiteId
     *
     * @return sring
     */
    public function getSourceWebsite($websiteId){
        $website = $this->websiteRepository->getById($websiteId);
        return $website->getName();
    }
}