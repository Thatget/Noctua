<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [06-06-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\MassAction\Tab\Renderer;

use Magento\Framework\DataObject;

class TemplateText extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory
     */
    protected $templatetextFactory;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\ResourceConnection                            $resource            [description]
     * @param \Magento\Framework\App\Request\Http                                  $request             [description]
     * @param \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory [description]
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Request\Http $request,
        \SIT\TemplateText\Model\ResourceModel\TemplateText\CollectionFactory $templatetextFactory
    ) {
        $this->_resource = $resource;
        $this->request = $request;
        $this->templatetextFactory = $templatetextFactory;
    }

    /**
     * [render description]
     * @param  DataObject $row [description]
     * @return [type]          [description]
     */
    public function render(DataObject $row)
    {
        $store_id = $this->request->getParam('store', 0);
        $name = '';
        if ($row['template_text_1'] != '-1') {
            $templateText1 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $row['template_text_1']])->getFirstItem();
            $name .= $templateText1->getTemplateTextTitle() . " (T1),";
        }
        if ($row['template_text_2'] != '-1') {
            $templateText2 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $row['template_text_2']])->getFirstItem();
            $name .= $templateText2->getTemplateTextTitle() . " (T2),";
        }
        if ($row['template_text_3'] != '-1') {
            $templateText3 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $row['template_text_3']])->getFirstItem();
            $name .= $templateText3->getTemplateTextTitle() . " (T3),";
        }
        if ($row['template_text_4'] != '-1') {
            $templateText4 = $this->templatetextFactory->create()->setStoreId($store_id)->addFieldToSelect('*')->addFieldToFilter("entity_id", ["eq" => $row['template_text_4']])->getFirstItem();
            $name .= $templateText4->getTemplateTextTitle() . " (T4),";
        }
        $tempName = rtrim($name, ', ');

        return $tempName;
    }
}
