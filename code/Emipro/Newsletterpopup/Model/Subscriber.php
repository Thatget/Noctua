<?php
namespace Emipro\Newsletterpopup\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Subscriber extends \Magento\Newsletter\Model\Subscriber
{

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Newsletter\Helper\Data $newsletterData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime = null,
        CustomerInterfaceFactory $customerFactory = null,
        DataObjectHelper $dataObjectHelper = null
    ) {
        parent::__construct($context, $registry, $newsletterData, $scopeConfig, $transportBuilder, $storeManager, $customerSession, $customerRepository, $customerAccountManagement, $inlineTranslation, $resource, $resourceCollection, $data, $dateTime, $customerFactory, $dataObjectHelper);
        $this->request = $request;
    }

    /**
     * Sends out unsubscription email
     *
     * @return $this
     */
    public function sendUnsubscriptionEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }
        if (!$this->_scopeConfig->getValue(
            self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) || !$this->_scopeConfig->getValue(
            self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier(
            $this->_scopeConfig->getValue(
                self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            ['subscriber' => $this]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $this->getEmail(),
            $this->getName()
        );

        /*Changed by MD for Stop unsubscribe email [START][24-06-2019]*/
        $actionName = $this->request->getActionName();

        if ($this->getIsBounced() != '1' && ($actionName == "massUnsubscribe" || $actionName == "unsubscribe")) {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            //Do not remove this log
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/bounced_emails.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r("Unsubscription email sent to " . $this->getEmail(), true));
        }
        /*Changed by MD for Stop unsubscribe email [END][24-06-2019]*/
        return $this;
    }

}
