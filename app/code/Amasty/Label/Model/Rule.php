<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

/**
 * Class Rule
 *
 * @package Amasty\Label\Model
 */
class Rule extends \Magento\CatalogRule\Model\Rule
{
    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $amastySerializer;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $storeEmulation;

    /**
     * @var \Amasty\Label\Plugin\App\Config\ScopeCodeResolver
     */
    private $scopeCodeResolver;

    /**
     * @var \Amasty\Label\Helper\Stock
     */
    private $stockHelper;

    protected function _construct()
    {
        $this->amastySerializer = $this->getData('amastySerializer');
        $this->stockHelper = $this->getData('stockHelper');
        $this->storeEmulation = $this->getData('storeEmulation');
        $this->scopeCodeResolver = $this->getData('scopeCodeResolver');
        if (!$this->amastySerializer) {
            $this->amastySerializer = $this->serializer;
        }
        parent::_construct();
        $this->_init(\Amasty\Label\Model\ResourceModel\Labels::class);
        $this->setIdFieldName('entity_id');
    }

    /**
     * @param array $ids
     */
    public function setProductFilter($ids)
    {
        $this->_productsFilter = $ids;
    }

    /**
     * create new function because it should be compatible with parent class
     * @param Labels $label
     *
     * @return array|null
     */
    public function getMatchingProductIdsByLabel(Labels $label)
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setCollectedAttributes([]);
            $this->scopeCodeResolver->setNeedClean(true);
            foreach (explode(',', $this->getStores()) as $storeId) {
                $this->storeEmulation->startEnvironmentEmulation($storeId);
                /** @var $productCollection ProductCollection */
                $productCollection = $this->_productCollectionFactory->create()
                    ->setStoreId($storeId);
                if ($label->getIsNew()) {
                    $this->joinDateAttributes($productCollection);
                }

                if ($label->getIsSale() || $label->getPriceRangeEnabled()) {
                    $this->joinPriceAttributes($productCollection);
                }

                $this->stockHelper->addStockFilter(
                    $productCollection,
                    $label->getStockStatus() == '0' ? null : ($label->getStockStatus() == '2' ? 1 : 0)
                );

                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }

                $this->getConditions()->collectValidatedAttributes($productCollection);

                $this->_resourceIterator->walk(
                    $productCollection->getSelect(),
                    [[$this, 'callbackValidateProduct']],
                    [
                        'attributes' => $this->getCollectedAttributes(),
                        'product' => $this->_productFactory->create(),
                        'store_id' => $storeId,
                        'label' => $label
                    ]
                );
                $this->storeEmulation->stopEnvironmentEmulation();
            }
        }

        return $this->_productIds;
    }

    /**
     * @param ProductCollection $productCollection
     */
    protected function joinDateAttributes(ProductCollection $productCollection)
    {
        $productCollection->addAttributeToSelect('created_at', 'left')
            ->addAttributeToSelect('news_from_date', 'left')
            ->addAttributeToSelect('news_to_date', 'left');
    }

    /**
     * @param ProductCollection $productCollection
     */
    protected function joinPriceAttributes(ProductCollection $productCollection)
    {
        $productCollection->addAttributeToSelect('special_price', 'left')
            ->addAttributeToSelect('special_from_date', 'left')
            ->addAttributeToSelect('special_to_date', 'left')
            ->addAttributeToSelect('price', 'left')
            ->addAttributeToSelect('price_type', 'left')
            ->addFinalPrice()
            ->addMinimalPrice();
    }

    /**
     * @param array $args
     */
    public function callbackValidateProduct($args)
    {
        $product = $args['product'];

        $storeId = $args['store_id'];
        $product->setData($args['row']);

        $product->setStoreId($storeId);

        $result = $this->getConditions()->validate($product);

        if ($result && isset($args['label'])) {
            $label = $args['label'];
            $product->setTypeInstance(null);
            $product->reloadPriceInfo();
            $label->init($product);

            $result = $result && $label->isApplicableForCustomRules();
        }

        if ($result) {
            $this->_productIds[$product->getId()][$storeId] = true;
        }
    }

    /**
     * fix fatal error after migration from 2.1 to 2.2 magento
     * Retrieve rule combine conditions model
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    public function getConditions()
    {
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }

        // Load rule conditions if it is applicable
        if ($this->hasConditionsSerialized()) {
            $conditions = $this->getConditionsSerialized();
            if (!empty($conditions)) {
                $conditions = $this->unserializeConditions($conditions);
                if (is_array($conditions) && !empty($conditions)) {
                    $this->_conditions->loadArray($conditions);
                }
            }
            $this->unsConditionsSerialized();
        }

        return $this->_conditions;
    }

    /**
     * @param $conditions
     *
     * @return array|bool|float|int|mixed|string|null
     */
    public function unserializeConditions($conditions)
    {
        $resultCondition = $this->amastySerializer->unserialize($conditions);
        if ($resultCondition === false) {
            $resultCondition = $this->serializer->unserialize($conditions);
        }

        return $resultCondition;
    }
}
