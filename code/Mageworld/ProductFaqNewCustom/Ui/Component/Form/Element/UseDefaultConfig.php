<?php
/**
 * Copyright © Mageworld, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageworld\ProductFaqNewCustom\Ui\Component\Form\Element;

use Magento\Framework\Data\ValueSourceInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Framework\Serialize\JsonValidator;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * Class UseDefaultConfig sets default value from configuration
 */
class UseDefaultConfig extends Checkbox
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var JsonValidator
     */
    private $jsonValidator;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory
     */
    protected $productFaqFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     * @param Json|null $serializer
     * @param JsonValidator|null $jsonValidator
     */
    public function __construct(
        ContextInterface $context,
        $components = [],
        array $data = [],
        Json $serializer = null,
        JsonValidator $jsonValidator = null,
        RequestInterface $request,
        \SIT\ProductFaqNew\Model\ResourceModel\ProductFaq\CollectionFactory $productFaqFactory
    ) {
        parent::__construct($context, $components, $data);
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->jsonValidator = $jsonValidator ?: ObjectManager::getInstance()->get(JsonValidator::class);
        $this->request = $request;
        $this->productFaqFactory = $productFaqFactory;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        $id = $this->request->getParam('entity_id');
        $storeId = $this->request->getParam('store', 0);

        $config = $this->getData('config');
        if (!$id || !$storeId) {
            $config['value'] = 0;
            $config['visible'] = 0;
        } else {
            $productfaqnewData = $this->productFaqFactory->create();
            $productfaqnew = $productfaqnewData->setStoreId($storeId)
                ->addFieldToSelect('*')
                ->addFieldToFilter("entity_id", ["eq" => $id])
                ->getFirstItem();

            if (!is_null($productfaqnew->getData($config['dataScope'])) &&
                (int)$productfaqnew->getData($config['dataScope']) == 0) {
                $config['value'] = 0;
            }
        }

        if (isset($config['keyInConfiguration'])
            && isset($config['valueFromConfig'])
            && $config['valueFromConfig'] instanceof ValueSourceInterface
        ) {
            $keyInConfiguration = $config['valueFromConfig']->getValue($config['keyInConfiguration']);
            if (!empty($config['unserialized']) && is_string($keyInConfiguration)) {
                if ($this->jsonValidator->isValid($keyInConfiguration)) {
                    $keyInConfiguration = $this->serializer->unserialize($keyInConfiguration);
                }
            }
            $config['valueFromConfig'] = $keyInConfiguration;
        }

        $this->setData('config', (array)$config);
        parent::prepare();
    }
}
