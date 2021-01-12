<?php
namespace Swissup\Easytabs\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Edit extends Action {
	/**
	 * Admin resource
	 */
	const ADMIN_RESOURCE = 'Swissup_Easytabs::easytabs_product_save';
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Registry $registry
	 */
	public function __construct(
		Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Registry $registry
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		parent::__construct($context);
	}

	/**
	 * Init actions
	 *
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	protected function _initAction() {
		// load layout, set active menu and breadcrumbs
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Swissup_Easytabs::easytabs')
			->addBreadcrumb(__('Easy Tabs'), __('Easy Tabs'))
			->addBreadcrumb(__('Manage Tabs'), __('Manage Tabs'));
		return $resultPage;
	}

	/**
	 * Edit Tab
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function execute() {
		$id = $this->getRequest()->getParam('tab_id');
		$model = $this->_objectManager->create('Swissup\Easytabs\Model\Entity');

		if ($id) {
			$model->load($id);
			try {
				if (isset($model['visible_tab'])) {
					if ($model['visible_tab'] == 'specific_product') {
						$model['product_key'] = implode(',', json_decode($model['visible_key']));
					}
					if ($model['visible_tab'] == 'specific_category') {
						$model['category_key'] = implode(',', json_decode($model['visible_key']));
					}
					if ($model['visible_tab'] == 'products') {
						if ($model['remove_from'] != null) {
							$model['remove_from_product'] = implode(',', json_decode($model['remove_from']));
						}
					}
					if ($model['visible_tab'] == 'category') {
						if ($model['remove_from'] != null) {
							$model['remove_from_category'] = implode(',', json_decode($model['remove_from']));
						}
					}
					if ($model['visible_tab'] == 'cms_page') {
						$model['cms_pages'] = json_decode($model['visible_key']);
					}
					if ($model['visible_tab'] == 'custom_page') {
						$model['custom_page'] = json_decode($model['visible_key']);
					}
				}
			} catch (Exception $e) {
				$this->messageManager->addError(__('Visible Tab Not Set.'));
			}
			// End By MJ [For visible_tab] [15.04.2019] /
			if (!$model->getId()) {
				$this->messageManager->addError(__('This tab no longer exists.'));
				/** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
				$resultRedirect = $this->resultRedirectFactory->create();

				return $resultRedirect->setPath('*/*/');
			}
		}

		$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		$this->_coreRegistry->register('easytab', $model);

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_initAction();
		$resultPage->addBreadcrumb(
			$id ? __('Edit Tab') : __('New Tab'),
			$id ? __('Edit Tab') : __('New Tab')
		);
		$resultPage->getConfig()->getTitle()->prepend(__('Easy Tabs'));
		$resultPage->getConfig()->getTitle()
			->prepend($model->getId() ? $model->getName() : __('New Tab'));

		return $resultPage;
	}
}
