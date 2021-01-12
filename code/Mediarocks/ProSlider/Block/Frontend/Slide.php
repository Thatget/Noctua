<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [26-03-2019]
 */
namespace Mediarocks\ProSlider\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mediarocks\ProSlider\Model\SlideFactory;

/**
 *Class Slide
 */
class Slide extends Template {

	/**
	 * @var SlideFactory
	 */
	protected $slideFactory;

	/**
	 * @param Context      $context      [description]
	 * @param SlideFactory $slideFactory [description]
	 */
	public function __construct(
		Context $context,
		SlideFactory $slideFactory
	) {
		$this->slideFactory = $slideFactory;
		parent::__construct($context);
	}

	/**
	 * To get slide collection by slide id
	 *
	 * @return array
	 */
	public function getSlideCollectionById($slide_id) {
		$collection = $this->slideFactory->create()->load($slide_id)->getData();
		return $collection;
	}
}