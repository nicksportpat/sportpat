<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model;

use Amasty\Base\Model\Serializer;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\GiftCard\Model\Product\ReadHandler;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Bundle\Model\Product\Type as BundleType;

class AbstractLabels extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Label cache tag
     */
    const CACHE_TAG = 'amasty_label';

    const TYPE_GIFTCARD = 'giftcard';

    public $_cacheTag = 'amasty_label';

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData = null;

    /**
     * @var bool
     */
    protected $isOutOfStockOnly = null;

    /**
     * Stock Registry
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Amasty\Label\Helper\Config
     */
    protected $helper;

    /**
     * @var  array
     */
    private $prices;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var Configurable
     */
    private $configurableType;

    /**
     * @var \Magento\GiftCard\Model\Product\ReadHandler
     */
    private $readHandler;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Amasty\Label\Model\RuleFactory $ruleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Amasty\Label\Helper\Config $helper,
        PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Amasty\Label\Model\GiftCard\Model\Product\ReadHandler $readHandler,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->catalogData = $catalogData;
        $this->stockRegistry = $stockRegistry;
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        $this->date = $date;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->setCacheTags([self::CACHE_TAG]);
        $this->configurableType = $configurableType;
        $this->readHandler = $readHandler;
    }

    /**
     * @param Product $product
     * @param null $mode
     * @param null $parent
     */
    public function init(Product $product, $mode = null, $parent = null)
    {
        $this->setProduct($product);
        $this->setParentProduct($parent);
        $this->prices = [];

        // auto detect product page
        if ($mode) {
            $this->setMode($mode == 'category' ? 'cat' : 'prod');
        } else {
            $this->setMode('cat');
        }
    }

    /**
     * @return bool
     */
    public function checkDateRange()
    {
        if (!$this->hasData(LabelInterface::DATE_RANGE_VALID)) {
            $result = true;
            $now = $this->timezone->date()->format('Y-m-d H:i:s');
            if ($this->getDateRangeEnabled()) {
                $fromDate = $this->getFromDate() ?: null;
                $toDate = $this->getToDate() ?: null;

                if (($fromDate !== null && $now < $fromDate)
                    || ($toDate !== null && $now > $toDate)
                ) {
                    $result = false;
                }
            }
            $this->setData(LabelInterface::DATE_RANGE_VALID, $result);
        }

        return $this->getData(LabelInterface::DATE_RANGE_VALID);
    }

    /**
     * @param array|null $ids
     *
     * @return array|null
     */
    public function getLabelMatchingProductIds($ids = null)
    {
        if ($this->getData('cond_serialize') !== '') {
            /** @var \Amasty\Label\Model\Rule $ruleModel */
            $ruleModel = $this->ruleFactory->create();
            $ruleModel->setConditions([]);
            $ruleModel->setStores($this->getData('stores'));
            $ruleModel->setConditionsSerialized($this->getData('cond_serialize'));
            if ($ids) {
                $ruleModel->setProductFilter($ids);
            }

            return $ruleModel->getMatchingProductIdsByLabel($this);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        if (!$this->getProduct()) {
            return false;
        }

        return $this->isApplicableForConditions() && $this->isApplicableForCustomRules();
    }

    /**
     * @return bool
     */
    public function isApplicableForConditions()
    {
        /** @var Product $product */
        $product = $this->getProduct();

        if ($this->getData('cond_serialize') != '') {
            $productIds = $this->getLabelMatchingProductIds([$product->getId()]);

            $inArray = array_key_exists($product->getId(), $productIds)
                && array_key_exists($product->getStore()->getId(), $productIds[$product->getId()]);

            if (!$inArray) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isApplicableForCustomRules()
    {
        /** @var Product $product */
        $product = $this->getProduct();

        $result = true;
        if (!$this->isPriceRange($product)
            || !$this->isStockRange($product)
            || !$this->isStockStatus($product)
            || !$this->isProductNew($product)
            || !$this->isOnSale()
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $product
     * @return bool
     */
    private function isPriceRange(Product $product)
    {
        $result = true;
        if ($this->getPriceRangeEnabled()) {
            $product = $this->loadPricesForGiftCard($product);
            $result = $this->getPriceCondition($product);
            if (!$result) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param Product $product
     *
     * @return Product
     */
    protected function loadPricesForGiftCard(Product $product)
    {
        if (self::TYPE_GIFTCARD == $product->getTypeId()) {
            $attribute = $product->getResource()->getAttribute('giftcard_amounts');
            if ($attribute->getId()) {
                $product = $this->readHandler->execute($product);
            }
        }

        return $product;
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function isStockRange(Product $product)
    {
        $result = true;
        $stockRangeEnabled = $this->getProductStockEnabled();
        if ($stockRangeEnabled == "1") {
            $qty = $this->getProductQty($product);
            $lessThan = $this->getStockLess();
            $higherThan = $this->getStockHigher();
            if ($lessThan !== null && $lessThan >= 0 && $lessThan <= $qty) {
                $result = false;
            }

            if ($higherThan !== null && $higherThan >= 0 && $higherThan >= $qty) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function isStockStatus(Product $product)
    {
        $result = true;
        $stockStatus = $this->getStockStatus();

        if ($this->isOutOfStockOnly()
            && !$this->getProductStockStatus($product)
            && ($stockStatus != 1 || $this->getId() != $this->getDefaultLabelId())
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    private function getProductStockStatus(Product $product)
    {
        return (bool) $product->hasData('is_salable')
            ? $product->getData('is_salable')
            : $product->getData('stock_status');
    }

    /**
     * @return bool
     */
    protected function isOutOfStockOnly()
    {
        if ($this->isOutOfStockOnly === null) {
            $this->isOutOfStockOnly = (bool)$this->helper->getModuleConfig('stock_status/out_of_stock_only');
        }

        return $this->isOutOfStockOnly;
    }

    /**
     * @return int
     */
    protected function getDefaultLabelId()
    {
        return (int)$this->helper->getModuleConfig('stock_status/default_label');
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function isProductNew(Product $product)
    {
        $result = true;
        if ($this->getIsNew()) {
            $isNew = $this->isNew($product) ? 2 : 1;
            if ($this->getIsNew() != $isNew) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param $product
     * @return bool
     */
    private function isOnSale()
    {
        $result = true;
        if ($this->getIsSale()) {
            $isSale = $this->isSale() ? 2 : 1;
            if ($this->getIsSale() != $isSale) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    private function getPriceCondition(Product $product)
    {
        $price = $this->getPrice($product);
        if ($product->getTypeId() == 'bundle') {
            $minimalPrice = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
            $maximalPrice = $product->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
            if ($minimalPrice < $this->getFromPrice() || $maximalPrice > $this->getToPrice()) {
                return false;
            }
        } elseif ($price < $this->getFromPrice() || $price > $this->getToPrice()) {
            return false;
        }

        return true;
    }

    /**
     * @param Product $product
     * @return float|int
     */
    private function getPrice(Product $product)
    {
        switch ($this->getByPrice()) {
            case '0': // Base Price
                $price = $this->catalogData->getTaxPrice($product, $product->getData('price'), false);
                break;
            case '1': // Special Price
                $price = $product->getPriceInfo()->getPrice('special_price')->getAmount()->getValue();
                break;
            case '2': // Final Price
                $price = $product->getPriceInfo()->getPrice('final_price')->getCustomAmount(null, 'tax')->getValue();
                break;
            case '3': // Final Price Incl Tax
                $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
                break;
            case '4': // Starting from Price
                $price = $this->getMinimalPrice($product);
                break;
            case '5': // Starting to Price
                $price = $this->getMaximalPrice($product);
                break;
            default:
                $price = 0;
                break;
        }

        return $price;
    }

    /**
     * @param Product $product
     *
     * @return float
     */
    protected function getMinimalPrice(Product $product)
    {
        $minimalPrice = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();

        if ($product->getTypeId() == Grouped::TYPE_CODE) {
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach ($associatedProducts as $item) {
                $temp = $this->catalogData->getTaxPrice($item, $item->getFinalPrice(), true);
                if ($minimalPrice === null || $temp < $minimalPrice) {
                    $minimalPrice = $temp;
                }
            }
        }

        return $minimalPrice;
    }

    /**
     * @param Product $product
     *
     * @return float|int
     */
    protected function getMaximalPrice(Product $product)
    {
        $maximalPrice = 0;
        if ($product->getTypeId() == Grouped::TYPE_CODE) {
            $associatedProducts = $this->getUsedProducts($product);
            foreach ($associatedProducts as $item) {
                $qty = $item->getQty() * 1 ? $item->getQty() * 1 : 1;
                $maximalPrice += $qty * $item->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            }
        }
        if (!$maximalPrice) {
            $maximalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        }

        return $maximalPrice;
    }

    /**
     * @param Product $product
     *
     * @return float|null
     */
    protected function getProductQty(Product $product)
    {
        $qty = null;
        if ($product->hasData('quantity_and_stock_status')) {
            $qty = $product->getData('quantity_and_stock_status')['qty'] ?? null;
        }

        if ($qty === null && $product->hasData('qty')) {
            $qty = (float)$product->getData('qty');
        }

        if ($qty === null && $product->hasData('quantity')) {
            $qty = (float)$product->getData('quantity');
        }

        return $qty;
    }

    /**
     * @param Product $product
     *
     * @return bool
     */
    private function isNew(Product $product)
    {
        $fromDate = '';
        $toDate = '';
        if ($this->helper->getModuleConfig('new/is_new')) {
            $fromDate = $product->getNewsFromDate();
            $toDate = $product->getNewsToDate();
        }

        if (!$fromDate && !$toDate) {
            return $this->getFromToDate($product);
        }

        $now = $this->timezone->date()->format('Y-m-d H:i:s');
        if ($fromDate && $now < $fromDate) {
            return false;
        }

        if ($toDate) {
            $toDate = str_replace('00:00:00', '23:59:59', $toDate);
            if ($now > $toDate) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function getFromToDate(Product $product)
    {
        if ($this->helper->getModuleConfig('new/creation_date')) {
            $days = $this->helper->getModuleConfig('new/days');
            if (!$days) {
                return false;
            }
            $createdAt = strtotime($product->getCreatedAt());
            $now = $this->timezone->date()->format('U');

            return ($now - $createdAt <= $days * 86400); // 60 sec. * 60 min. * 24 hours = 86400 sec.
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isSale()
    {
        if (in_array($this->getProduct()->getTypeId(), ['giftcard', 'amgiftcard'])) {
            return false;
        }

        $price = $this->loadPrices();
        if ($price['price'] <= 0 || !$price['special_price']) {
            return false;
        }

        // in dollars
        $diff = $price['price'] - $price['special_price'];
        $min = $this->helper->getModuleConfig('on_sale/sale_min');
        if ($diff < 0.001 || ($min && $diff < $min)) {
            return false;
        }

        // in percents
        $value = ceil($diff * 100 / $price['price']);
        $minPercent = $this->helper->getModuleConfig('on_sale/sale_min_percent');
        if ($minPercent && $value < $minPercent) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    protected function loadPrices()
    {
        if (!$this->prices) {
            /** @var Product $product */
            $product = $this->getProduct();
            /** @var Product $parent */
            $parent = $this->getParentProduct();

            $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            if ($product->getTypeId() == Configurable::TYPE_CODE) {
                $regularPrice = $this->priceCurrency->convertAndRound($regularPrice);
            }

            $specialPrice = $this->getSpecialPrice($product);

            if ($parent && ($parent->getTypeId() == Grouped::TYPE_CODE)) {
                $usedProds = $product->getTypeInstance(true)->getAssociatedProducts($product);
                foreach ($usedProds as $child) {
                    if ($child->getId() != $product->getId()) {
                        $regularPrice += $child->getPrice();
                        $specialPrice += $child->getFinalPrice();
                    }
                }
            }
            $this->prices = [
                'price' => $regularPrice,
                'special_price' => $specialPrice
            ];
        }

        return $this->prices;
    }

    /**
     * @param $product
     * @return int
     */
    private function getSpecialPrice(Product $product)
    {
        $specialPrice = 0;
        if ($this->getIsSale() && $this->getSpecialPriceOnly()) {
            $now = $this->getCompareDate();
            if ((!$product->getSpecialFromDate() || $now >= $this->getCompareDate($product->getSpecialFromDate()))
                && !$product->getSpecialToDate() || $now <= $this->getCompareDate($product->getSpecialToDate())
            ) {
                $specialPrice = $product->getPriceInfo()->getPrice('special_price')->getAmount()->getValue();
            }
        } else {
            $specialPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        }

        return $specialPrice;
    }

    /**
     * Get value by label mode
     * @return string
     */
    public function getValue($key)
    {
        $data = $this->getData($this->getMode() . '_' . $key);

        return $data;
    }

    /**
     * @return array|bool|string
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = [];
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = [$this->_cacheTag];
                }

                $idTags = $this->getCacheIdTags();
                if ($idTags) {
                    $tags = array_merge($tags, $idTags);
                }
            }
        }

        return $tags;
    }

    /**
     * Get cahce tags associated with object id
     *
     * @return array|bool
     */
    public function getCacheIdTags()
    {
        $tags = false;
        if ($this->getId() && $this->_cacheTag) {
            $tags = [];
            if (is_array($this->_cacheTag)) {
                foreach ($this->_cacheTag as $_tag) {
                    $tags[] = $_tag . '_' . $this->getId();
                }
            } else {
                $tags[] = $this->_cacheTag . '_' . $this->getId();
            }
        }
        return $tags;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Label\Model\ResourceModel\Labels::class);
        $this->setIdFieldName('label_id');
    }

    /**
     * @param Product $product
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    private function getUsedProducts(Product $product)
    {
        $result = [];
        switch ($product->getTypeId()) {
            case Configurable::TYPE_CODE:
                $result = $this->configurableType->getUsedProducts($product);
                break;
            case Grouped::TYPE_CODE:
                $result = $product->getTypeInstance(true)->getAssociatedProducts($product);
                break;
            case BundleType::TYPE_CODE:
                $result = $product->getTypeInstance(true)->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product),
                    $product
                );
                break;
        }

        return $result;
    }

    /**
     * @param string|null $date
     *
     * @return string
     */
    private function getCompareDate($date = null)
    {
        return $this->timezone->scopeDate(
            $this->storeManager->getStore($this->getProduct()->getStoreId()),
            $date
        )->format('Y-m-d');
    }
}
