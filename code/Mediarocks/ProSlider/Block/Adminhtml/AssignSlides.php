<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace Mediarocks\ProSlider\Block\Adminhtml;

use Magento\Backend\Block\Template;

class AssignSlides extends Template {

	/**
	 * Block template
	 *
	 * @var string
	 */
	protected $_template = 'slides/assign_slides.phtml';

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

	/**
	 * @var \Mediarocks\ProSlider\Model\SliderFactory
	 */
	protected $_sliderCollectionFactory;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\Block\Template\Context   $context                 [description]
	 * @param \Magento\Framework\Registry               $registry                [description]
	 * @param \Magento\Framework\Json\EncoderInterface  $jsonEncoder             [description]
	 * @param \Mediarocks\ProSlider\Model\SliderFactory $sliderCollectionFactory [description]
	 * @param array                                     $data                    [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\Mediarocks\ProSlider\Model\SliderFactory $sliderCollectionFactory,
		array $data = []
	) {
		$this->registry = $registry;
		$this->jsonEncoder = $jsonEncoder;
		$this->_sliderCollectionFactory = $sliderCollectionFactory;
		parent::__construct($context, $data);
	}

	/**
	 * Retrieve instance of grid block
	 *
	 * @return \Magento\Framework\View\Element\BlockInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getBlockGrid() {
		if (null === $this->blockGrid) {
			$this->blockGrid = $this->getLayout()->createBlock(
				'Mediarocks\ProSlider\Block\Adminhtml\Tab\Slidegrid',
				'mediarocks.proslider.tab.productgrid'
			);
		}
		return $this->blockGrid;
	}

	/**
	 * Return HTML of grid block
	 *
	 * @return string
	 */
	public function getGridHtml() {
		return $this->getBlockGrid()->toHtml();
	}

	/**
	 * @return string
	 */
	public function getProductsJson() {

		$entity_id = $this->getRequest()->getParam('entity_id');

		$sliderCollectionFactory = $this->_sliderCollectionFactory->create()->load($entity_id);

		if (isset($sliderCollectionFactory)) {
			$result = $sliderCollectionFactory['slides'];
			$result = explode(',', $result);
			return $this->jsonEncoder->encode($result);
		}
		return '{}';
	}

	public function getItem() {
		return $this->registry->registry('my_item');
	}
}
