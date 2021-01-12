<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\ProductCompatibility\Controller\Adminhtml\Mainboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SIT\ProductCompatibility\Helper\Data as ProductCompHelper;
use SIT\ProductCompatibility\Model\Product as SitProductComp;
use SIT\ProductCompatibility\Model\ProductCompatibilityFactory;
use SIT\ProductCompatibility\Model\ProductFactory as SitProductCompFactory;

class Save extends Action
{

    /**
     * @var ProductCompatibilityFactory
     */
    protected $productcompFactory;

    /**
     * @var SitProductComp
     */
    protected $sitProductCompProduct;

    /**
     * @var SitProductCompFactory
     */
    protected $sitProductCompFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $entityAttribute;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $attributeOptionCollectionFactory;

    /**
     * @var ProductCompHelper
     */
    protected $prodCompHelper;

    /**
     * [__construct description]
     * @param Context                                                                    $context                          [description]
     * @param ProductCompatibilityFactory                                                $productcompFactory               [description]
     * @param SitProductComp                                                             $sitProductCompProduct            [description]
     * @param SitProductCompFactory                                                      $sitProductCompFactory            [description]
     * @param \Magento\Eav\Model\Entity\Attribute                                        $entityAttribute                  [description]
     * @param \Magento\Eav\Setup\EavSetupFactory                                         $eavSetupFactory                  [description]
     * @param \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute                $attributeFactory                 [description]
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory [description]
     * @param ProductCompHelper                                                          $prodCompHelper                   [description]
     */
    public function __construct(
        Context $context,
        ProductCompatibilityFactory $productcompFactory,
        SitProductComp $sitProductCompProduct,
        SitProductCompFactory $sitProductCompFactory,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \SIT\ProductCompatibility\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionCollectionFactory,
        ProductCompHelper $prodCompHelper
    ) {
        $this->productcompFactory = $productcompFactory;
        $this->sitProductCompProduct = $sitProductCompProduct;
        $this->sitProductCompFactory = $sitProductCompFactory;
        $this->entityAttribute = $entityAttribute;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeFactory = $attributeFactory;
        $this->attributeOptionCollectionFactory = $attributeOptionCollectionFactory;
        $this->prodCompHelper = $prodCompHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SIT_ProductCompatibility::mainboard');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        //print_r($data['comp_manufacture']);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = [13562,
                13563,
                13564,
                13565,
                13567,
                13568,
                13569,
                13570,
                13571,
                13572,
                13573,
                13574,
                13575,
                13576,
                13577,
                13578,
                13579,
                13580,
                13581,
                13582,
                13583,
                13584,
                13585,
                13586,
                13587,
                13588,
                13589,
                13590,
                13596,
                13597,
                13598,
                13599,
                13600,
                13601,
                13602,
                13603,
                13604,
                13605,
                13606,
                13607,
                13608,
                13609,
                13610,
                13611,
                13612,
                13613,
                13614,
                13615,
                13616,
                13617,
                13618,
                13619,
                13620,
                13621,
                13622,
                13623,
                13624,
                13625,
                13626,
                13627,
                13628,
                13629,
                13630,
                13631,
                13632,
                13633,
                13634,
                13635,
                13636,
                13637,
                13638,
                13639,
                13640,
                13641,
                13642,
                13643,
                13644,
                13645,
                13646,
                13647,
                13648,
                13649,
                13650,
                13651,
                13652,
                13653,
                13654,
                13655,
                13656,
                13657,
                13658,
                13659,
                13660,
                13661,
                13662,
                13663,
                13664,
                13665,
                13666,
                13667,
                13668,
                13669,
                13670,
                13671,
                13672,
                13673,
                13674,
                13675,
                13676,
                13677,
                13678,
                13679,
                13680,
                13681,
                13682,
                13683,
                13684,
                13685,
                13686,
                13687,
                13688,
                13689,
                13690,
                13691,
                13692,
                13693,
                13694,
                13695,
                13696,
                13697,
                13698,
                13699,
                13700,
                13701,
                13702,
                13703,
                13704,
                13705,
                13706,
                13707,
                13708,
                13709,
                13710,
                13711,
                13712,
                13713,
                13714,
                13715,
                13716,
                13717,
                13718,
                13719,
                13720,
                13721,
                13722,
                13723,
                13724,
                13725,
                13726,
                13727,
                13728,
                13729,
                13730,
                13731,
                13732,
                13733,
                13734];
            $params = [];
            $productcompInstance = $this->productcompFactory->create();
            $productcompInstance->setStoreId($storeId);
            echo "<pre>";
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('eav_attribute_option_value');

            //Select Data from table

            foreach ($data as $key => $value) {
                $productcompInstance->load($value);
                $model = $productcompInstance->getCompModel();
                if ($model) {
                    $sqlMod = "Select value FROM eav_attribute_option_value where option_id =" . $model;
                    $compModel = $connection->fetchOne($sqlMod); // gives associated array, table fields as key in array.
                }
                $manu = $productcompInstance->getCompManufacture();
                if ($manu) {
                    $sqlMan = "Select value FROM eav_attribute_option_value where option_id =" . $manu;
                    $compManu = $connection->fetchOne($sqlMan); // gives associated array, table fields as key in array.
                }

                $urlKey = str_replace(' ', '_', $compManu) . "_" . str_replace(' ', '_', $compModel);
                $productcompInstance->setData('url_key', $urlKey);
                $productcompInstance->save();

            }
            exit();
            /*$params['store'] = $storeId;
            $urlKey = str_replace(' ', '_', $data['comp_manufacture']) . "_" . str_replace(' ', '_', $data['comp_model']);
            if (empty($data['entity_id'])) {
            $data["url_key"] = $urlKey;
            $data['entity_id'] = null;
            } else {*/
            /*    $params['entity_id'] = $data['entity_id'];
        $checkUrl = $this->checkUrlKey($data['entity_id'], $urlKey);
        if ($checkUrl) {
        $data["url_key"] = $urlKey;
        }
        }*/

        }
    }

}
