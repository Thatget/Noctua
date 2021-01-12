<?php
namespace SIT\MainAdmin\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface {
	/**
	 * EAV setup factory.
	 *
	 * @var EavSetupFactory
	 */
	private $_eavSetupFactory;
	protected $categorySetupFactory;

	/**
	 * Init.
	 *
	 * @param EavSetupFactory $eavSetupFactory
	 */
	public function __construct(EavSetupFactory $eavSetupFactory, \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory) {
		$this->_eavSetupFactory = $eavSetupFactory;
		$this->categorySetupFactory = $categorySetupFactory;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	) {
		/** @var EavSetup $eavSetup */
		$eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
		$setup = $this->categorySetupFactory->create(['setup' => $setup]);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'faq_list_position',
			[
				'type' => 'varchar',
				'label' => 'Position (FAQ listing and selection dropdown)',
				'input' => 'text',
				'sort_order' => 100,
				'source' => '',
				'global' => 1,
				'visible' => true,
				'required' => false,
				'user_defined' => false,
				'default' => null,
				'group' => '',
				'backend' => '',
			]
		);
	}
}
