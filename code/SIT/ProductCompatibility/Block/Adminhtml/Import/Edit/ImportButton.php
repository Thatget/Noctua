<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Code standard by : MD [28-05-2019]
 */
namespace SIT\ProductCompatibility\Block\Adminhtml\Import\Edit;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ImportButton implements ButtonProviderInterface
{

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * [__construct description]
     * @param Context          $context [description]
     * @param RequestInterface $request [description]
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }
    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $storeId = $this->request->getParam('store', 0);
        return [
            'label' => __('Import Compatibilities'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'sit_productcompatibility_import_form.sit_productcompatibility_import_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'compimport' => 'importcsvdata',
                                        'store' => $storeId,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
