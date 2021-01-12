<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [1-3-2019]
 */
namespace SIT\GeneralTechnologiesNew\Block\GeneralTechnology;

use SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory;

class Technologies extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory
     */
    protected $generalTechnologyFactory;

    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $template;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SIT\MainAdmin\Helper\Data
     */
    protected $sitHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Zend_Filter_Interface
     */
    protected $templateProcessor;

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context $context                  [description]
     * @param CollectionFactory                                $generalTechnologyFactory [description]
     * @param \Magento\Framework\View\Element\Template         $template                 [description]
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager             [description]
     * @param \SIT\MainAdmin\Helper\Data                       $sitHelper                [description]
     * @param \Magento\Framework\App\Request\Http              $request                  [description]
     * @param \Zend_Filter_Interface                           $templateProcessor        [description]
     * @param array                                            $data                     [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $generalTechnologyFactory,
        \Magento\Framework\View\Element\Template $template,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SIT\MainAdmin\Helper\Data $sitHelper,
        \Magento\Framework\App\Request\Http $request,
        \Zend_Filter_Interface $templateProcessor,
        array $data = []
    ) {
        $this->generalTechnologyFactory = $generalTechnologyFactory;
        $this->template = $template;
        $this->storeManager = $storeManager;
        $this->sitHelper = $sitHelper;
        $this->request = $request;
        $this->templateProcessor = $templateProcessor;
        parent::__construct($context, $data);
    }

    /**
     * Return news details when custom routing action call
     *
     * @return \SIT\GeneralTechnologiesNew\Model\ResourceModel\GeneralTechnology\CollectionFactory
     */
    public function getTechnologiesDetails()
    {
        $currentStore = $this->_storeManager->getStore()->getId();
        $techColl = $this->generalTechnologyFactory->create()->setStoreId($currentStore)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1]);
        $techArr = [];
        foreach ($techColl as $key => $tech) {
            $techArr[$key]['tech_id'] = $tech->getId();
            $techArr[$key]['tech_title'] = $tech->getGenTechnologyTitle();
            $techArr[$key]['tech_short_desc'] = $this->sitHelper->getCmsFilterContent($tech->getGenTechnologyShortdesc());
            $techArr[$key]['tech_url_key'] = $tech->getUrlKey();
            $techArr[$key]['tech_image'] = $this->sitHelper->getImage('generaltechnology/image', $tech->getGenTechnologyImage());
            $techArr[$key]['tech_desc'] = $tech->getGenTechnologyDesc();
        }
        return $techArr;
    }

    /**
     * Get Current Page Id
     */
    public function getCurrentPageId()
    {
        return $this->request->getParam('tech_id');
    }

    /**
     * Get Base Url
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get Pub Static image
     */
    public function getSkinUrl()
    {
        $imageUrl = $this->template->getViewFileUrl("SIT_GeneralTechnologiesNew::images/duble-arrow-blue.png");
        return $imageUrl;
    }

    /**
     * Convert Wyswing data
     * @param  String $string [description]
     * @return String         [description]
     */
    public function getHtmlContentData($string)
    {
        return $this->templateProcessor->filter($string);
    }
}
