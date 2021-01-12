<?php
namespace SIT\ProductTechNew\Block\Adminhtml;

class AssignProducts extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'products/assign_products.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    protected $_productCollectionFactory;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context  $context                  [description]
     * @param \Magento\Framework\Registry              $registry                 [description]
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder              [description]
     * @param \SIT\ProductTechNew\Model\ProductFactory $productCollectionFactory [description]
     * @param array                                    $data                     [description]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \SIT\ProductTechNew\Model\ProductFactory $productCollectionFactory, //your custom collection
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'SIT\ProductTechNew\Block\Adminhtml\Tab\Productgrid',
                'category.product.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $entity_id = $this->getRequest()->getParam('entity_id');
        $productCollectionFactory = $this->_productCollectionFactory->create()->getCollection();
        $productCollectionFactory->addFieldToSelect('product_id');
        $productCollectionFactory->addFieldToSelect('position');
        $productCollectionFactory->addFieldToFilter('producttechnology_id', $entity_id);
        if (!empty($productCollectionFactory->getData())) {
            foreach ($productCollectionFactory->getData() as $techProducts) {
                $result[$techProducts['product_id']] = $techProducts['position'];
            }
            return $this->jsonEncoder->encode($result);
        }
        return '{}';
    }

    public function getItem()
    {
        return $this->registry->registry('my_item');
    }
}
