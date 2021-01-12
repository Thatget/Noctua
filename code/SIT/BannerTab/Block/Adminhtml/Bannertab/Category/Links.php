<?php
namespace SIT\BannerTab\Block\Adminhtml\Bannertab\Category;

class Links extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'bannertab/category/links.phtml';

    /**
     * @var \Mediarocks\ProSlider\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context   $context         [description]
     * @param \Mediarocks\ProSlider\Model\SliderFactory $sliderFactory   [description]
     * @param \Magento\Catalog\Model\CategoryFactory    $categoryFactory [description]
     * @param array                                     $data            [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mediarocks\ProSlider\Model\SliderFactory $sliderFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }

    /**
     * [getCategorySlider description]
     * @param  [type] $categoryId [description]
     * @return [type]             [description]
     */
    public function getCategorySlider($categoryId)
    {
        $category = $this->categoryFactory->create()->load($categoryId);
        $data = [];
        $categoryUrlKey = $category->getUrlKey();
        $collection = $this->sliderFactory->create()->getCollection()->addAttributeToSelect(['category', 'title', 'page_type'])->addAttributeToFilter('page_type', ['eq', 'category']);
        foreach ($collection as $key => $value) {
            $sliderCategories = json_decode(unserialize($value->getCategory()));
            if (in_array($categoryUrlKey, $sliderCategories)) {
                $data[] = $value->getTitle();
                $data[] = $this->getUrl('proslider/slider/edit/entity_id/' . $value->getEntityId() . '/');
                break;
            }
        }
        return $data;
    }
}
