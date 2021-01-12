<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\AjaxFanSearch\Block\AjaxFanSearch;

use NumberFormatter;

class ListFanData extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Json\Encoder
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \SIT\MainAdmin\Helper\Data
     */
    protected $sitHelper;

    const FAN_CATEGORY_KEY = 'fan';

    const REDUX_CATEGORY_KEY = 'line-redux';

    const INDUSTRIAL_CATEGORY_KEY = 'line-industrial';

    const CHROMAX_CATEGORY_KEY = 'line-chromax';

    const SIZE_GROUP_IMAGE = 'size_group_image';

    const SIZE_GROUP_HEADER_IMAGE = 'size_group_header_image';

    const SIZE_MM_ATTR_TEXT = 'size_mm';

    const CONNECTOR_ATTR_TEXT = 'connector';

    const VOLTAGE_ATTR_TEXT = 'voltage';

    /**
     * [__construct description]
     * @param \Magento\Framework\View\Element\Template\Context               $context                  [description]
     * @param \Magento\Eav\Model\Config                                      $eavConfig                [description]
     * @param \Magento\Framework\Json\Encoder                                $jsonEncoder              [description]
     * @param \Magento\Catalog\Model\CategoryFactory                         $categoryFactory          [description]
     * @param \Magento\Catalog\Model\ProductFactory                          $productFactory           [description]
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory [description]
     * @param \SIT\MainAdmin\Helper\Data                                     $sitHelper                [description]
     * @param array                                                          $data                     [description]
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Json\Encoder $jsonEncoder,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \SIT\MainAdmin\Helper\Data $sitHelper,
        array $data = []
    ) {
        $this->eavConfig = $eavConfig;
        $this->jsonEncoder = $jsonEncoder;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sitHelper = $sitHelper;
        parent::__construct($context, $data);
    }

    /**
     * Return voltage value by attribute code an category url key vise
     *
     * @param  int $attributeCode
     * @param  int $catUrlkeys
     * @return \Magento\Eav\Model\Config
     */
    public function getFanSearchVoltageValue($attr_code, $catUrlkeys)
    {
        $category = $this->categoryFactory->create();
        $categoryInstance = $category->getCollection()->addAttributeToFilter('url_key', ['in' => $catUrlkeys])->addAttributeToSelect(['entity_id']);
        $catalog_ids = [];
        foreach ($categoryInstance as $key => $value) {
            $catalog_ids[$key]['id'] = $value['entity_id'];
        }
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addCategoriesFilter(['in' => $catalog_ids])->addAttributeToSelect($attr_code);
        $productCollection->addStoreFilter();
        $productCollection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $productCollection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $voltage_Arr = [];
        foreach ($productCollection as $key => $product) {
            $attribute_value = $product->getResource()->getAttribute('voltage')->getFrontend()->getValue($product);
            if (!in_array(trim($attribute_value), $voltage_Arr) && trim($attribute_value) != "") {
                $voltage_Arr[] = $attribute_value;
            }
        }
        rsort($voltage_Arr);
        $final_voltage_val = [];
        foreach ($voltage_Arr as $key => $value) {
            $final_voltage_val[$key]['value'] = trim($value);
            $final_voltage_val[$key]['label'] = trim($value) . " V";
            $final_voltage_val[$key]['data_text'] = 'voltage';
        }
        return $this->jsonEncoder->encode($final_voltage_val);
    }

    /**
     * Return Fan Search Attribute Options for Knockout File
     *
     * @param  int $attributeCode
     * @return \Magento\Eav\Model\Config
     */
    public function getFanSearchAttrOptions($attributeCode)
    {
        $options = $this->getAttributeList($attributeCode);
        foreach ($options as $key => $value) {
            $options[$key]['data_text'] = $attributeCode;
        }
        return $this->jsonEncoder->encode($options);
    }

    /**
     * Return Fan Search Attribute Options for Filter
     *
     * @param  int $attrCode
     * @return \Magento\Eav\Model\Config
     */
    public function getAttributeList($attrCode)
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attrCode);
        $options = $attribute->getSource()->getAllOptions();
        $keys = array_column($options, 'value');
        array_multisort($keys, SORT_DESC, $options);
        return $options;
    }

    /**
     * Return Fan Search Product List as per Category Vise
     *
     * @param  string $catUrlKey
     * @return array
     */
    public function getFanSearchProList($catUrlKey = null, $text_label = null, $attrFanSize = null)
    {
        $small_img_arr = [];
        $fan_small_img = [];
        $redux_small_img = [];
        $ind_small_img = [];
        $chromax_small_img = [];
        $sortData = [];
        $fanDisplayData = [];

        $category = $this->categoryFactory->create();
        if ($catUrlKey == null) {
            $currentCategory = $this->sitHelper->getCurrentCategory();
            $catUrlKey = $currentCategory->getUrlKey();
        }

        $categoryId = $category->loadByAttribute('url_key', $catUrlKey)->getId();

        if ($attrFanSize == null) {
            $attrFanSize = $this->getSizeAttrProOptions($categoryId, $this->getAttributeList('size_mm'));
        }
        if ($catUrlKey == "fan") {
            $url_keys = [self::FAN_CATEGORY_KEY, self::REDUX_CATEGORY_KEY, self::INDUSTRIAL_CATEGORY_KEY, self::CHROMAX_CATEGORY_KEY];
        } else {
            $url_keys = [$catUrlKey];
        }

        $fan_small_img = $this->getGrpSmallImg($attrFanSize, self::FAN_CATEGORY_KEY, self::SIZE_GROUP_IMAGE, self::SIZE_MM_ATTR_TEXT);
        $redux_small_img = $this->getGrpSmallImg($attrFanSize, self::REDUX_CATEGORY_KEY, self::SIZE_GROUP_IMAGE, self::SIZE_MM_ATTR_TEXT);
        $ind_small_img = $this->getGrpSmallImg($attrFanSize, self::INDUSTRIAL_CATEGORY_KEY, self::SIZE_GROUP_IMAGE, self::SIZE_MM_ATTR_TEXT);
        $chromax_small_img = $this->getGrpSmallImg($attrFanSize, self::CHROMAX_CATEGORY_KEY, self::SIZE_GROUP_IMAGE, self::SIZE_MM_ATTR_TEXT);
        $small_img_arr[self::FAN_CATEGORY_KEY] = $fan_small_img;
        $small_img_arr[self::REDUX_CATEGORY_KEY] = $redux_small_img;
        $small_img_arr[self::INDUSTRIAL_CATEGORY_KEY] = $ind_small_img;
        $small_img_arr[self::CHROMAX_CATEGORY_KEY] = $chromax_small_img;

        $categoryInstance = $category->getCollection()->addAttributeToFilter('url_key', ['in' => $url_keys])->addAttributeToSelect(['entity_id']);
        $catalog_ids = [];
        foreach ($categoryInstance as $key => $value) {
            $catalog_ids[$key]['id'] = $value['entity_id'];
            $catalog_ids[$key]['url_key'] = $value['url_key'];
        }
        $attribute_select = ['size', 'size_mm', 'acoustical_noise', 'name', 'url_key', 'size_group_image', 'size_group_header_image', 'voltage', 'rotational_speed_10per', 'airflow', 'static_pressure', 'connector', 'position'];
        $attrFanCount = 0;
        foreach ($attrFanSize as $key => $value) {
            if (trim($value['label']) != '' && trim($value['value']) != '') {
                $i = 0;
                foreach ($catalog_ids as $catalog_id) {
                    $temp_count = 0;
                    $productCollection = $category->load($catalog_id['id'])->getProductCollection()->addAttributeToSelect('*')->addAttributeToSort('position', 'ASC');
                    $productCollection->addAttributeToFilter('size_mm', ['eq' => $value['value']]);

                    /**
                     * For filter data according to ajax filter from particular tab
                     */
                    if ($this->getData('connector_value') != "") {
                        $productCollection->addAttributeToFilter('connector', ['eq' => $this->getData('connector_value')]);
                    }
                    if ($this->getData('voltage_value') != "") {
                        $productCollection->addAttributeToFilter('voltage', ['eq' => $this->getData('voltage_value')]);
                    }
                    $productCollection->setOrder('size_mm', 'DESC');
                    $productCollection->setOrder('entity_id', 'ASC');
                    if ($catUrlKey == 'fan') {
                        $grp_header_img = $this->getGrpHeaderImg($value['value'], 'fan,line-redux,line-industrial,line-chromax', self::SIZE_GROUP_HEADER_IMAGE, self::SIZE_MM_ATTR_TEXT);
                    } else {
                        $grp_header_img = $this->getGrpHeaderImg($value['value'], $catUrlKey, self::SIZE_GROUP_HEADER_IMAGE, self::SIZE_MM_ATTR_TEXT);
                    }
                    if ($grp_header_img != 'noimg') {
                        if (count($productCollection) > 0) {
                            $fanDisplayData['group_title'][$value['text']] = $value['label'];
                            $fanDisplayData['group_img'][$value['text']] = $this->sitHelper->getImage('catalog/product', $grp_header_img);
                            $fanDisplayData['group_pin_img'][$value['text']] = $this->sitHelper->getImage('default/images', '/b-img.jpg');
                            $product_arr = [];
                            foreach ($productCollection as $key => $product) {
                                $product_arr[$temp_count]['name'] = $product->getData('name');
                                $product_arr[$temp_count]['url_key'] = $product->getData('url_key');
                                if ($product->getData('size_group_image') != "no_selection") {
                                    $fanDisplayData['group_small_img'][$value['text']][$catalog_id['url_key']] = $this->sitHelper->getImage('catalog/product', $small_img_arr[$catalog_id['url_key']][$value['value']]);
                                } else {
                                    /**
                                     * For set blank velue in 'group_small_img' key. When filter will apply & small image not found.
                                     */
                                    if ($this->getData('connector_value') != "" || $this->getData('fan_size_value') != "" || $this->getData('voltage_value') != "") {
                                        $fanDisplayData['group_small_img'][$value['text']][$catalog_id['url_key']] = $this->sitHelper->getImage('catalog/product', $small_img_arr[$catalog_id['url_key']][$value['value']]);
                                    }
                                }

                                $product_arr[$temp_count]['size_mm'] = $product->getData('size_mm');
                                $product_arr[$temp_count]['acoustical_noise'] = $product->getData('acoustical_noise');
                                $product_arr[$temp_count]['voltage'] = $product->getData('voltage');
                                $product_arr[$temp_count]['rotational_speed_10per'] = $product->getData('rotational_speed_10per');
                                $product_arr[$temp_count]['airflow'] = $product->getData('airflow');
                                $product_arr[$temp_count]['static_pressure'] = $product->getData('static_pressure');
                                $product_arr[$temp_count]['connector'] = $product->getData('connector');
                                $product_arr[$temp_count]['connector_text'] = $this->getOptionLabelByValue('connector', $product->getData('connector'));
                                $product_arr[$temp_count]['size_text'] = $this->getOptionLabelByValue('size', $product->getData('size'));
                                $product_arr[$temp_count]['product_url'] = $product->getProductUrl();
                                $temp_count++;
                            }
                            $sortData[$attrFanCount][$value['text']][$i][$catalog_id['url_key']] = $product_arr;
                        }
                    }
                }
                $attrFanCount++;
                $i++;
            }

        }
        $newSortData = array_values($sortData);
        $fanDisplayData['collection'] = $newSortData;
        $fanDisplayData['faq_link'] = $this->sitHelper->getStoreBaseUrl() . 'productfaqs/productfaq/view/id/162';
        $fanDisplayData['right_fan_link'] = $this->sitHelper->getStoreBaseUrl() . 'which_fan_is_right_for_me';
        return $this->jsonEncoder->encode($fanDisplayData);
    }

    /**
     * Return Product Small Images for Fan,Redux,Chromax as per URL Key
     *
     * @param  array $options
     * @param  string $url_key
     * @param  string $size_group_image
     * @param  string $attr_text
     * @return array
     */
    public function getGrpSmallImg($options, $url_key, $size_group_image, $attr_text)
    {
        $array_val = [];
        foreach ($options as $key => $value) {
            $products = $this->categoryFactory->create()->loadByAttribute('url_key', $url_key)->getProductCollection()->addAttributeToSelect([$size_group_image, $attr_text]);
            $products->addAttributeToFilter($attr_text, ['eq' => $value['value']]);
            $products->addAttributeToFilter($size_group_image, ['neq' => 'no_selection']);
            $products->addStoreFilter();
            $products->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $products->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            if ($products->getSize() > 0) {
                $array_val[$value['value']] = $products->getFirstItem()->getSizeGroupImage();
            } else {
                $array_val[$value['value']] = 'noimg';
            }
        }
        return $array_val;
    }

    /**
     * Return Product Group Header Images for Fan,Redux,Chromax as per URL Key
     *
     * @param  array $options
     * @param  string $url_key
     * @param  string $size_group_image
     * @param  string $attr_text
     * @return array
     */
    public function getGrpHeaderImg($options, $url_key, $size_group_image, $attr_text)
    {
        $array_val = [];
        $products = $this->categoryFactory->create()->loadByAttribute('url_key', ['in' => $url_key])->getProductCollection()->addAttributeToSelect([$size_group_image, $attr_text]);
        $products->addAttributeToFilter($attr_text, ['eq' => $options]);
        $products->addAttributeToFilter($size_group_image, ['neq' => 'no_selection']);
        $products->addStoreFilter();
        $products->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $products->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        if ($products->getSize() > 0) {
            $array_val = $products->getFirstItem()->getSizeGroupHeaderImage();
        } else {
            $array_val = 'noimg';
        }
        return $array_val;
    }

    public function getSizeAttrProOptions($categoryId, $attr_coll)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addCategoriesFilter(['in' => $categoryId])->addAttributeToSelect('size_mm');

        /**
         * For filter data according to ajax filter from particular tab
         */
        if ($this->getData('fan_size_value') != "") {
            $productCollection->addAttributeToFilter('size_mm', ['eq' => $this->getData('fan_size_value')]);
        }

        $productCollection->setOrder('size_mm', 'DESC');
        $productCollection->setOrder('entity_id', 'ASC');
        $attr_arr = [];
        foreach ($productCollection as $key => $value) {
            $attr_arr[] = $value['size_mm'];
        }
        $test = array_unique($attr_arr);
        $sizeCount = 0;
        $return_attr_val = [];
        foreach ($test as $key => $value) {
            $return_attr_val[$sizeCount]['value'] = $value;
            $return_attr_val[$sizeCount]['label'] = $this->getOptionLabelByValue(self::SIZE_MM_ATTR_TEXT, $value);
            $return_attr_val[$sizeCount]['text'] = $this->getNoToWord($this->getOptionLabelByValue(self::SIZE_MM_ATTR_TEXT, $value));
            $return_attr_val[$sizeCount]['text_label'] = self::SIZE_MM_ATTR_TEXT;
            $sizeCount++;
        }

        return $return_attr_val;
    }

    /**
     * Get Label by option id
     *
     * @param  [type] $attributeCode [description]
     * @param  [type] $optionId      [description]
     * @return [type]                [description]
     */
    public function getOptionLabelByValue($attributeCode, $optionId)
    {
        $_product = $this->productFactory->create();
        $isAttributeExist = $_product->getResource()->getAttribute($attributeCode);
        $optionText = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionText = $isAttributeExist->getSource()->getOptionText($optionId);
        }
        return $optionText;
    }

    public function getNoToWord($string)
    {
        $no_to_word = preg_split('#(?<=\d)(?=[a-z])#i', $string);
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $string_from_no = $f->format($no_to_word[0]);
        return str_replace(' ', '_', $string_from_no);
    }
}
