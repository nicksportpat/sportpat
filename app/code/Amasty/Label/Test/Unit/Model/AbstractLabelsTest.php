<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Model;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\AbstractLabels;
use Amasty\Label\Model\RuleFactory;
use Amasty\Label\Test\Unit\Traits;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AbstractLabelsTest
 *
 * @see AbstractLabels
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class AbstractLabelsTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var AbstractLabels
     */
    private $model;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|MockObject
     */
    private $timezone;

    /**
     * @var \Amasty\Label\Helper\Config|MockObject
     */
    private $helper;

    /**
     * @var \Amasty\Label\Helper\Config|MockObject
     */
    private $configurableType;

    protected function setUp()
    {
        $this->timezone = $this->getMockBuilder(
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface::class)
            ->setMethods(['date', 'format', 'scopeDate'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ruleFactory = $this->getMockBuilder(RuleFactory::class)
            ->setMethods(['create', 'setConditions', 'setStores', 'setConditionsSerialized', 'getMatchingProductIdsByLabel'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $stockRegistry = $this->getMockBuilder(
            \Magento\CatalogInventory\Api\StockRegistryInterface::class)
            ->setMethods(['getStockItem', 'getStockStatusBySku'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $stockItem = $this->createMock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $catalogData = $this->createMock(\Magento\Catalog\Helper\Data::class);
        $storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $this->helper = $this->createMock(\Amasty\Label\Helper\Config::class);
        $this->configurableType = $this->createMock(Configurable::class);
        $stockStatus = $this->getMockBuilder(\Magento\CatalogInventory\Api\Data\StockStatusInterface::class)
            ->setMethods(['setWebsiteId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $ruleFactory->expects($this->any())->method('create')->willReturn($ruleFactory);
        $ruleFactory->expects($this->any())->method('getMatchingProductIdsByLabel')->willReturn([]);
        $stockRegistry->expects($this->any())->method('getStockItem')->willReturn($stockItem);
        $stockRegistry->expects($this->any())->method('getStockStatusBySku')->willReturn($stockStatus);
        $stockItem->expects($this->any())->method('getQty')->willReturn(10);
        $catalogData->expects($this->any())->method('getTaxPrice')->willReturnOnConsecutiveCalls(10, 1);

        $this->model = $this->getObjectManager()->getObject(
            AbstractLabels::class,
            [
                'timezone' => $this->timezone,
                'ruleFactory' => $ruleFactory,
                'stockRegistry' => $stockRegistry,
                'catalogData' => $catalogData,
                'helper' => $this->helper,
                'storeManager' => $storeManager,
                'configurableType' => $this->configurableType,
            ]
        );
    }

    /**
     * @covers AbstractLabels::checkDateRange
     */
    public function testCheckDateRange()
    {
        $this->timezone->expects($this->any())->method('date')->willReturn($this->timezone);
        $this->timezone->expects($this->any())->method('format')->willReturn('2019-01-01 00:00:00');
        $this->assertTrue($this->model->checkDateRange());

        //check value from cache
        $this->assertTrue($this->model->checkDateRange());

        $this->model->unsetData(LabelInterface::DATE_RANGE_VALID);
        $this->model->setDateRangeEnabled(true);
        $this->model->setFromDate('2020-02-02 00:00:00');
        $this->model->setToDate('2018-02-02 00:00:00');
        $this->assertFalse($this->model->checkDateRange());
    }

    /**
     * @covers AbstractLabels::getLabelMatchingProductIds
     */
    public function testGetLabelMatchingProductIds()
    {
        $this->assertEquals([], $this->model->getLabelMatchingProductIds());
        $this->model->setData('cond_serialize', '');
        $this->assertNull($this->model->getLabelMatchingProductIds());
    }

    /**
     * @covers AbstractLabels::isApplicable
     */
    public function testIsApplicable()
    {
        $model = $this->createPartialMock(
            AbstractLabels::class,
            ['getProduct', 'isApplicableForConditions', 'isApplicableForCustomRules']
        );
        $model->expects($this->any())->method('getProduct')->willReturnOnConsecutiveCalls(false, true);
        $model->expects($this->any())->method('isApplicableForConditions')->willReturn(true);
        $model->expects($this->any())->method('isApplicableForCustomRules')->willReturn(true);

        $this->assertFalse($model->isApplicable());
        $this->assertTrue($model->isApplicable());
    }

    /**
     * @covers AbstractLabels::isApplicableForConditions
     */
    public function testIsApplicableForConditions()
    {
        $model = $this->createPartialMock(
            AbstractLabels::class,
            ['getProduct', 'getLabelMatchingProductIds', 'getData']
        );
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $model->expects($this->any())->method('getProduct')->willReturn($product);
        $model->expects($this->any())->method('getLabelMatchingProductIds')->willReturn([1, [2, 3]]);
        $model->expects($this->any())->method('getData')->willReturnOnConsecutiveCalls('', 'test');
        $product->expects($this->any())->method('getId')->willReturn(4);
        $product->expects($this->any())->method('getStore')->willReturn($product);

        $this->assertTrue($model->isApplicableForConditions());
        $this->assertFalse($model->isApplicableForConditions());
    }

    /**
     * @covers AbstractLabels::isPriceRange
     */
    public function testIsPriceRange()
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $this->assertTrue($this->invokeMethod($this->model, 'isPriceRange', [$product]));

        $this->model->setPriceRangeEnabled(true);
        $this->model->setFromPrice(1);
        $this->assertFalse($this->invokeMethod($this->model, 'isPriceRange', [$product]));
    }

    /**
     * @covers AbstractLabels::isStockRange
     */
    public function testIsStockRange()
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $this->assertTrue($this->invokeMethod($this->model, 'isStockRange', [$product]));
        $this->model->setProductStockEnabled(true);
        $this->model->setStockLess(5);
        $this->assertTrue($this->invokeMethod($this->model, 'isStockRange', [$product]));
        $this->model->setStockLess(null);
        $this->model->setStockHigher(20);
        $this->assertFalse($this->invokeMethod($this->model, 'isStockRange', [$product]));
    }

    /**
     * @covers AbstractLabels::isStockStatus
     */
    public function testIsStockStatus()
    {
        $product = $this->createPartialMock(
            \Magento\Catalog\Model\Product::class,
            ['getStore', 'getWebsiteId', 'getSku']
        );
        $product->expects($this->any())->method('getStore')->willReturn($product);
        $product->expects($this->any())->method('getWebsiteId')->willReturn($product);
        $this->setProperty($this->model, 'isOutOfStockOnly', false);
        $this->assertTrue($this->invokeMethod($this->model, 'isStockStatus', [$product]));

        $product->setData('is_salable', false);
        $this->setProperty($this->model, 'isOutOfStockOnly', true);
        $this->assertFalse($this->invokeMethod($this->model, 'isStockStatus', [$product]));

        $product->setData('is_salable', true);
        $this->assertTrue($this->invokeMethod($this->model, 'isStockStatus', [$product]));
    }

    /**
     * @covers AbstractLabels::isProductNew
     */
    public function testIsProductNew()
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $this->assertTrue($this->invokeMethod($this->model, 'isProductNew', [$product]));
        $this->model->setIsNew(2);
        $this->assertFalse($this->invokeMethod($this->model, 'isProductNew', [$product]));
    }

    /**
     * @covers AbstractLabels::isOnSale
     */
    public function testIsOnSale()
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $price = $this->createMock(\Magento\Framework\Pricing\Price\PriceInterface::class);
        $product->expects($this->any())->method('getPriceInfo')->willReturn($product);
        $product->expects($this->any())->method('getPrice')->willReturn($price);
        $price->expects($this->any())->method('getAmount')->willReturn($price);
        $this->assertTrue($this->invokeMethod($this->model, 'isOnSale'));
        $this->model->setIsSale(2);
        $this->model->setProduct($product);
        $this->assertFalse($this->invokeMethod($this->model, 'isOnSale'));
    }

    /**
     * @covers AbstractLabels::getPriceCondition
     */
    public function testGetPriceCondition()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $price = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getPrice', 'getMinimalPrice', 'getValue', 'getMaximalPrice'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $price->expects($this->any())->method('getPrice')->willReturn($price);
        $price->expects($this->any())->method('getMinimalPrice')->willReturn($price);
        $price->expects($this->any())->method('getMaximalPrice')->willReturn($price);
        $price->expects($this->any())->method('getValue')->willReturn(5);

        $this->assertTrue($this->invokeMethod($this->model, 'getPriceCondition', [$product]));
        $this->model->setFromPrice(5);
        $this->assertFalse($this->invokeMethod($this->model, 'getPriceCondition', [$product]));
        $this->model->setFromPrice(0);
        $this->model->setToPrice(-1);
        $this->assertFalse($this->invokeMethod($this->model, 'getPriceCondition', [$product]));
        $product->setTypeId('bundle');
        $product->setPriceInfo($price);
        $this->setProperty($product, '_priceInfo', $price, \Magento\Catalog\Model\Product::class);
        $this->model->setFromPrice(6);
        $this->assertFalse($this->invokeMethod($this->model, 'getPriceCondition', [$product]));
        $this->model->setFromPrice(1);
        $this->model->setToPrice(1);
        $this->assertFalse($this->invokeMethod($this->model, 'getPriceCondition', [$product]));
    }

    /**
     * @covers AbstractLabels::getPrice
     */
    public function testGetPrice()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $price = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getPrice', 'getMinimalPrice', 'getValue', 'getMaximalPrice', 'getAmount', 'getCustomAmount'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $minPrice = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $customAmountPrice = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $price->expects($this->any())->method('getPrice')->willReturn($price);
        $price->expects($this->any())->method('getAmount')->willReturn($price);
        $price->expects($this->any())->method('getCustomAmount')->willReturn($customAmountPrice);
        $price->expects($this->any())->method('getMinimalPrice')->willReturn($minPrice);
        $price->expects($this->any())->method('getMaximalPrice')->willReturn($price);
        $price->expects($this->any())->method('getValue')->willReturn(5);
        $minPrice->expects($this->any())->method('getValue')->willReturn(1);
        $customAmountPrice->expects($this->any())->method('getValue')->willReturn(2);

        $this->setProperty($product, '_priceInfo', $price, \Magento\Catalog\Model\Product::class);

        $this->assertEquals(0, $this->invokeMethod($this->model, 'getPrice', [$product]));
        $this->model->setByPrice(0);
        $this->assertEquals(10, $this->invokeMethod($this->model, 'getPrice', [$product]));
        $this->model->setByPrice(1);
        $this->assertEquals(5, $this->invokeMethod($this->model, 'getPrice', [$product]));
        $this->model->setByPrice(2);
        $this->assertEquals(2, $this->invokeMethod($this->model, 'getPrice', [$product]));
        $this->model->setByPrice(3);
        $this->assertEquals(5, $this->invokeMethod($this->model, 'getPrice', [$product]));
        $this->model->setByPrice(4);
        $this->assertEquals(1, $this->invokeMethod($this->model, 'getPrice', [$product]));
        $this->model->setByPrice(5);
        $this->assertEquals(5, $this->invokeMethod($this->model, 'getPrice', [$product]));
    }

    /**
     * @covers AbstractLabels::getMinimalPrice
     */
    public function testGetMinimalPrice()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $product1 = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class)->setFinalPrice(1);
        $product2 = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class)->setFinalPrice(5);
        $price = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getPrice', 'getMinimalPrice', 'getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $type = $this->getMockBuilder(\Magento\Catalog\Model\Product\Type\AbstractType::class)
            ->setMethods(['getAssociatedProducts'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $price->expects($this->any())->method('getPrice')->willReturn($price);
        $price->expects($this->any())->method('getMinimalPrice')->willReturn($price);
        $price->expects($this->any())->method('getValue')->willReturn(5);
        $type->expects($this->any())->method('getAssociatedProducts')->willReturn([$product1, $product2]);

        $this->setProperty($product, '_priceInfo', $price, \Magento\Catalog\Model\Product::class);
        $this->assertEquals(5, $this->invokeMethod($this->model, 'getMinimalPrice', [$product]));
        $product->setTypeId(Grouped::TYPE_CODE);
        $this->setProperty($product, '_typeInstance', $type, \Magento\Catalog\Model\Product::class);
        $this->setProperty($product1, '_calculatePrice', false, \Magento\Catalog\Model\Product::class);
        $this->setProperty($product2, '_calculatePrice', false, \Magento\Catalog\Model\Product::class);
        $this->assertEquals(1, $this->invokeMethod($this->model, 'getMinimalPrice', [$product]));
    }

    /**
     * @covers AbstractLabels::getMaximalPrice
     */
    public function testGetMaximalPrice()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $price = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getPrice', 'getAmount', 'getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $type = $this->getMockBuilder(\Magento\Catalog\Model\Product\Type\AbstractType::class)
            ->setMethods(['getAssociatedProducts'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $product1 = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class)->setFinalPrice(1);
        $product2 = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class)->setFinalPrice(10);

        $price->expects($this->any())->method('getPrice')->willReturn($price);
        $price->expects($this->any())->method('getAmount')->willReturn($price);
        $price->expects($this->any())->method('getValue')->willReturn(5);
        $type->expects($this->any())->method('getAssociatedProducts')->willReturn([$product1, $product2]);

        $this->setProperty($product, '_priceInfo', $price);

        $this->assertEquals(5, $this->invokeMethod($this->model, 'getMaximalPrice', [$product]));

        $product->setTypeId(Grouped::TYPE_CODE);
        $this->setProperty($product, '_typeInstance', $type);
        $product1->setQty(5);
        $this->setProperty($product1, '_priceInfo', $price);
        $this->setProperty($product2, '_priceInfo', $price);
        $this->assertEquals(30, $this->invokeMethod($this->model, 'getMaximalPrice', [$product]));
    }

    /**
     * @covers AbstractLabels::getProductQty
     */
    public function testGetProductQty()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $product->setData('quantity', 10);
        $this->assertEquals(10, $this->invokeMethod($this->model, 'getProductQty', [$product]));
        $product->setData('qty', 20);
        $this->assertEquals(20, $this->invokeMethod($this->model, 'getProductQty', [$product]));
    }

    /**
     * @covers AbstractLabels::isNew
     */
    public function testIsNew()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $this->helper->expects($this->any())->method('getModuleConfig')->willReturn(true);
        $this->timezone->expects($this->any())->method('date')->willReturn($this->timezone);
        $this->timezone->expects($this->any())->method('format')->willReturn('2019-06-01 00:00:00');

        $product->setNewsFromDate('2019-07-01 00:00:00');
        $product->setNewsToDate('2019-05-01 00:00:00');
        $this->assertFalse($this->invokeMethod($this->model, 'isNew', [$product]));
        $product->setNewsFromDate('2019-04-01 00:00:00');
        $this->assertFalse($this->invokeMethod($this->model, 'isNew', [$product]));
        $product->setNewsToDate('2019-07-01 00:00:00');
        $this->assertTrue($this->invokeMethod($this->model, 'isNew', [$product]));
    }

    /**
     * @covers AbstractLabels::getFromToDate
     */
    public function testGetFromToDate()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $this->helper->expects($this->any())->method('getModuleConfig')
            ->willReturnOnConsecutiveCalls(false, true, false, true, 10);
        $this->timezone->expects($this->any())->method('date')->willReturn($this->timezone);
        $this->timezone->expects($this->any())->method('format')->willReturn(1550076800);

        $this->assertFalse($this->invokeMethod($this->model, 'getFromToDate', [$product]));
        $this->assertFalse($this->invokeMethod($this->model, 'getFromToDate', [$product]));
        $product->setCreatedAt('2019-04-01 00:00:00');
        $this->assertTrue($this->invokeMethod($this->model, 'getFromToDate', [$product]));
    }

    /**
     * @covers AbstractLabels::isSale
     */
    public function testIsSale()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $this->helper->expects($this->any())->method('getModuleConfig')->willReturnOnConsecutiveCalls(5, 5, 1, 5);

        $this->model->setProduct($product);
        $product->setTypeId('giftcard');
        $this->assertFalse($this->invokeMethod($this->model, 'isSale'));

        $product->setTypeId('amgiftcard');
        $this->assertFalse($this->invokeMethod($this->model, 'isSale'));

        $product->setTypeId('test');
        $this->setProperty($this->model, 'prices', ['price' => 0], AbstractLabels::class);
        $this->assertFalse($this->invokeMethod($this->model, 'isSale'));

        $this->setProperty($this->model, 'prices', ['price' => 1, 'special_price' => 0], AbstractLabels::class);
        $this->assertFalse($this->invokeMethod($this->model, 'isSale'));

        $this->setProperty($this->model, 'prices', ['price' => 5, 'special_price' => 2], AbstractLabels::class);
        $this->assertFalse($this->invokeMethod($this->model, 'isSale'));

        $this->setProperty($this->model, 'prices', ['price' => 1, 'special_price' => 2], AbstractLabels::class);
        $this->assertFalse($this->invokeMethod($this->model, 'isSale'));

        $this->setProperty($this->model, 'prices', ['price' => 300, 'special_price' => 297], AbstractLabels::class);
        $this->assertFalse($this->invokeMethod($this->model, 'isSale'));

        $this->setProperty($this->model, 'prices', ['price' => 3, 'special_price' => 2], AbstractLabels::class);
        $this->assertTrue($this->invokeMethod($this->model, 'isSale'));
    }

    /**
     * @covers AbstractLabels::loadPrices
     */
    public function testLoadPrices()
    {
        $parentProduct = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $price = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getPrice', 'getAmount', 'getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $type = $this->getMockBuilder(\Magento\Catalog\Model\Product\Type\AbstractType::class)
            ->setMethods(['getAssociatedProducts'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $product1 = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class)->setFinalPrice(1);
        $product2 = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class)->setFinalPrice(10);

        $type->expects($this->any())->method('getAssociatedProducts')->willReturn([$product1, $product2]);
        $price->expects($this->any())->method('getPrice')->willReturn($price);
        $price->expects($this->any())->method('getAmount')->willReturn($price);
        $price->expects($this->any())->method('getValue')->willReturn(5);

        $product1->setId(1)->setPrice(10)->setFinalPrice(4);
        $product2->setId(2)->setPrice(2)->setFinalPrice(5);

        $this->setProperty($product, '_priceInfo', $price);
        $this->model->setProduct($product);
        $this->model->setParentProduct($parentProduct);
        $this->assertEquals(['price' => 5, 'special_price' => 5], $this->invokeMethod($this->model, 'loadPrices'));

        $parentProduct->setTypeId(Grouped::TYPE_CODE);
        $this->model->setParentProduct($parentProduct);
        $this->setProperty($this->model, 'prices', null, AbstractLabels::class);
        $this->setProperty($product, '_typeInstance', $type);
        $this->setProperty($product1, '_calculatePrice', false);
        $this->setProperty($product2, '_calculatePrice', false);
        $this->assertEquals(['price' => 17, 'special_price' => 14], $this->invokeMethod($this->model, 'loadPrices'));
    }

    /**
     * @covers AbstractLabels::getSpecialPrice
     */
    public function testGetSpecialPrice()
    {
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $price = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods(['getPrice', 'getAmount', 'getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $price->expects($this->any())->method('getPrice')->willReturn($price);
        $price->expects($this->any())->method('getAmount')->willReturn($price);
        $price->expects($this->any())->method('getValue')->willReturn(5);
        $this->timezone->expects($this->any())->method('scopeDate')->willReturn($this->timezone);
        $this->timezone->expects($this->any())->method('format')->willReturn('2019-01-01');

        $this->setProperty($product, '_priceInfo', $price);
        $this->assertEquals(5, $this->invokeMethod($this->model, 'getSpecialPrice', [$product]));

        $product->setStoreId(1);
        $this->model->setIsSale(true);
        $this->model->setSpecialPriceOnly(true);
        $this->model->setProduct($product);
        $this->assertEquals(5, $this->invokeMethod($this->model, 'getSpecialPrice', [$product]));
    }

    /**
     * @covers AbstractLabels::getCacheTags
     */
    public function testGetCacheTags()
    {
        $this->assertEquals(['amasty_label'], $this->model->getCacheTags());
        $this->model->setId(5);
        $this->model->_cacheTag = ['test'];
        $this->assertEquals(['test', 'test_5'], $this->model->getCacheTags());
        $this->model->_cacheTag = true;
        $this->assertEquals([], $this->model->getCacheTags());
        $this->model->_cacheTag = null;
        $this->assertFalse($this->model->getCacheTags());
    }

    /**
     * @covers AbstractLabels::getCacheIdTags
     */
    public function testGetCacheIdTags()
    {
        $this->model->setId(5);
        $this->assertEquals(['amasty_label_5'], $this->model->getCacheIdTags());
        $this->model->_cacheTag = ['test1', 'test2'];
        $this->assertEquals(['test1_5', 'test2_5'], $this->model->getCacheIdTags());
    }

    /**
     * @covers AbstractLabels::getUsedProducts
     */
    public function testGetUsedProducts()
    {
        $product = $this->createPartialMock(
            \Magento\Catalog\Model\Product::class,
            ['getTypeId']
        );
        $type = $this->getMockBuilder(\Magento\Catalog\Model\Product\Type\AbstractType::class)
            ->setMethods(['getAssociatedProducts', 'getSelectionsCollection', 'getOptionsIds'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $type->expects($this->once())->method('getAssociatedProducts');
        $type->expects($this->once())->method('getSelectionsCollection');
        $type->expects($this->once())->method('getOptionsIds');
        $product->expects($this->any())->method('getTypeId')
            ->willReturnOnConsecutiveCalls(Configurable::TYPE_CODE, Grouped::TYPE_CODE, BundleType::TYPE_CODE);
        $this->configurableType->expects($this->once())->method('getUsedProducts');

        $this->setProperty($product, '_typeInstance', $type);
        $this->invokeMethod($this->model, 'getUsedProducts', [$product]);
        $this->invokeMethod($this->model, 'getUsedProducts', [$product]);
        $this->invokeMethod($this->model, 'getUsedProducts', [$product]);
    }
}
