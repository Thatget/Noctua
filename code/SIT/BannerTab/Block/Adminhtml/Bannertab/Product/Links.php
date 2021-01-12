<?php
namespace SIT\BannerTab\Block\Adminhtml\Bannertab\Product;

class Links extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'bannertab/product/links.phtml';

    /**
     * @var \Mediarocks\ProSlider\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context   $context        [description]
     * @param \Mediarocks\ProSlider\Model\SliderFactory $sliderFactory  [description]
     * @param \Magento\Catalog\Model\ProductFactory     $productFactory [description]
     * @param array                                     $data           [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mediarocks\ProSlider\Model\SliderFactory $sliderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param  int $productId [description]
     * @return array            [description]
     */
    public function getProductSlider($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        $data = [];
        /*Changed by MD for check Slider SKU with Product SKU[START][25-09-2019]*/
        $productSku = $product->getSku();
        /*Changed by MD for check Slider SKU with Product SKU[END][25-09-2019]*/
        $collection = $this->sliderFactory->create()->getCollection()->addAttributeToSelect(['sku', 'title', 'page_type'])->addAttributeToFilter('page_type', ['eq', 'product']);
        foreach ($collection as $key => $value) {
            $sliderSku = json_decode(unserialize($value->getSku()));
            if (in_array($productSku, $sliderSku)) {
                $data[] = $value->getTitle();
                $data[] = $this->getUrl('proslider/slider/edit/entity_id/' . $value->getEntityId() . '/');
                break;
            }
        }
        return $data;
    }
}
