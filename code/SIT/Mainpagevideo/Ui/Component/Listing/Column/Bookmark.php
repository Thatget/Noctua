<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : Rh [1-3-2019]
 */
namespace SIT\Mainpagevideo\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;

class Bookmark extends \Magento\Ui\Component\Bookmark
{
    /**
     * @var \SIT\Mainpagevideo\Model\Mainpagevideo
     */
    protected $mainpageVideo;

    /**
     * [__construct description]
     * @param ContextInterface                       $context            [description]
     * @param \SIT\Mainpagevideo\Model\Mainpagevideo $mainpageVideo      [description]
     * @param BookmarkRepositoryInterface            $bookmarkRepository [description]
     * @param BookmarkManagementInterface            $bookmarkManagement [description]
     * @param array                                  $components         [description]
     * @param array                                  $data               [description]
     */
    public function __construct(
        ContextInterface $context,
        \SIT\Mainpagevideo\Model\Mainpagevideo $mainpageVideo,
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkManagementInterface $bookmarkManagement,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $bookmarkRepository, $bookmarkManagement, $components, $data);
        $this->mainpageVideo = $mainpageVideo;
    }

    /**
     * Register component
     *
     * @return void
     */
    public function prepare()
    {
        $namespace = $this->getContext()->getRequestParam('namespace', $this->getContext()->getNamespace());
        $config = [];
        if (!empty($namespace)) {
            $storeId = $this->getContext()->getRequestParam('store');

            if (empty($storeId)) {
                $storeId = $this->getContext()->getFilterParam('store_id');
            }

            $bookmarks = $this->bookmarkManagement->loadByNamespace($namespace);
            /** @var \Magento\Ui\Api\Data\BookmarkInterface $bookmark */
            foreach ($bookmarks->getItems() as $bookmark) {
                if ($bookmark->isCurrent()) {
                    $config['activeIndex'] = $bookmark->getIdentifier();
                }

                $config = array_merge_recursive($config, $bookmark->getConfig());

                if (!empty($storeId)) {
                    $config['current']['filters']['applied']['store_id'] = $storeId;
                }
            }
        }

        $this->setData('config', array_replace_recursive($config, $this->getConfiguration($this)));

        parent::prepare();

        $jsConfig = $this->getConfiguration($this);
        $this->getContext()->addComponentDefinition($this->getComponentName(), $jsConfig);
    }
}
