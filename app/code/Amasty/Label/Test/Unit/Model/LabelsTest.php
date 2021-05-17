<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Model;

use Amasty\Label\Model\Labels;
use Amasty\Label\Model\AbstractLabels;
use Amasty\Label\Test\Unit\Traits;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class LabelsTest
 *
 * @see Labels
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class LabelsTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var Labels
     */
    private $model;

    /**
     * @var \Amasty\Label\Helper\Config
     */
    private $helper;

    protected function setUp()
    {
        $priceCurrency = $this->createMock(PriceCurrencyInterface::class);
        $storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $catalogData = $this->createMock(\Magento\Catalog\Helper\Data::class);
        $this->helper = $this->createMock(\Amasty\Label\Helper\Config::class);

        $priceCurrency->expects($this->any())->method('convertAndFormat')->willReturnArgument(0);
        $catalogData->expects($this->any())->method('getTaxPrice')->willReturnArgument(2);

        $this->model = $this->getObjectManager()->getObject(
            Labels::class,
            [
                'priceCurrency' => $priceCurrency,
                'storeManager' => $storeManager,
                'catalogData' => $catalogData,
                'helper' => $this->helper,
            ]
        );
    }

    /**
     * @covers Labels::getText
     * @dataProvider getTextDataProvider
     */
    public function testGetText($text, $result, $value)
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);

        $product->expects($this->any())->method('getSku')->willReturn('test');

        $this->model->setMode($text);
        $this->model->setData($text . '_txt', $value);
        $this->model->setProduct($product);
        $this->assertEquals($result, $this->model->getText());
    }

    /**
     * Data provider for getText test
     * @return array
     */
    public function getTextDataProvider()
    {
        return [
            ['test', null, ''],
            ['test_21', 'test', '{SKU}']
        ];
    }

    /**
     * @covers Labels::getPrice
     * @dataProvider getPriceDataProvider
     */
    public function testGetPrice($var, $result)
    {
        $model = $this->createPartialMock(
            Labels::class,
            ['loadPrices', 'getMinimalPrice', 'getMaximalPrice', 'getPercentPrice', 'getProductQty']
        );
        $type = $this->getMockBuilder(\Magento\Catalog\Model\Product\Type\AbstractType::class)
            ->setMethods(['getSku'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $date = $this->createMock(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        $priceCurrency = $this->createMock(PriceCurrencyInterface::class);
        $catalogData = $this->createMock(\Magento\Catalog\Helper\Data::class);
        $this->setProperty($model, 'storeManager', $storeManager);
        $this->setProperty($model, 'priceCurrency', $priceCurrency);
        $this->setProperty($model, 'catalogData', $catalogData);
        $this->setProperty($model, 'date', $date);
        $product = $this->getObjectManager()->getObject(\Magento\Catalog\Model\Product::class);
        $this->setProperty($product, '_typeInstance', $type);

        $model->expects($this->any())->method('loadPrices')->willReturn(['price' => 10, 'special_price' => 5]);
        $model->expects($this->any())->method('getMinimalPrice')->willReturn(20);
        $model->expects($this->any())->method('getMaximalPrice')->willReturn(30);
        $model->expects($this->any())->method('getPercentPrice')->willReturn(40);
        $model->expects($this->any())->method('getProductQty')->willReturn(50);
        $type->expects($this->any())->method('getSku')->willReturn('sku');
        $date->expects($this->any())->method('date')->willReturn('2019-01-01 00:00:00 GMT');
        $priceCurrency->expects($this->any())->method('convertAndFormat')->willReturnArgument(0);
        $catalogData->expects($this->any())->method('getTaxPrice')->willReturnArgument(2);

        $product->setFinalPrice(1);
        $product->setCreatedAt('2019-01-01');
        $product->setSpecialToDate('2019-05-05 00:00:00 GMT');
        $this->setProperty($product, '_calculatePrice', false);
        $this->assertEquals($result, $this->invokeMethod($model, 'getCustomVariable', [$var, $product]));
    }

    /**
     * Data provider for getPrice test
     * @return array
     */
    public function getPriceDataProvider()
    {
        return [
            ['PRICE', 10],
            ['SPECIAL_PRICE', 5],
            ['FINAL_PRICE', false],
            ['FINAL_PRICE_INCL_TAX', true],
            ['STARTINGFROM_PRICE', 20],
            ['STARTINGTO_PRICE', 30],
            ['SAVE_AMOUNT', 5],
            ['SAVE_PERCENT', 40],
            ['BR', '<br/>'],
            ['SKU', 'sku'],
            ['NEW_FOR', max(1, floor((time() - 1546300800) / 86400))],
            ['STOCK', 50],
            ['SPDL', 124.0], // can be failed depend on phpstorm rounding
            ['SPHL', 2976.0],// can be failed depend on phpstorm roun
            ['test', ''],
        ];
    }

    /**
     * @covers Labels::getPercentPrice
     * @dataProvider getPercentPriceDataProvider
     */
    public function testGetPercentPrice($rounding, $result)
    {
        $this->helper->expects($this->any())->method('getModuleConfig')->willReturnCallback(
            function () use ($rounding) {
                return $rounding;
            }
        );
        $this->setProperty($this->model, 'prices', ['price' => 10.5, 'special_price' => 5.2], AbstractLabels::class);
        $this->assertEquals($result, $this->invokeMethod($this->model, 'getPercentPrice'));
    }

    /**
     * Data provider for getPercentPrice test
     * @return array
     */
    public function getPercentPriceDataProvider()
    {
        return [
            ['floor', 50],
            ['ceil', 51],
            ['round', 50],
            ['test', 50],
        ];
    }

    /**
     * @covers Labels::getDefaultValue
     * @dataProvider getDefaultValueDataProvider
     */
    public function testGetDefaultValue($var, $result, $attrType)
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $productResource = $this->createMock(Product::class);
        $attribute = $this->getMockBuilder(AbstractAttribute::class)
            ->setMethods(['getFrontendInput'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $product->expects($this->any())->method('getResource')->willReturn($productResource);
        $product->expects($this->any())->method('getData')->willReturn('test');
        $product->expects($this->any())->method('getAttributeText')->willReturn(['test.1', 'test2']);
        $productResource->expects($this->any())->method('getAttribute')->willReturn($attribute);
        $attribute->expects($this->any())->method('getFrontendInput')->willReturnCallback(
            function () use ($attrType) {
                return $attrType;
            }
        );

        $this->assertEquals($result, $this->invokeMethod($this->model, 'getDefaultValue', [$product, $var]));
    }

    /**
     * Data provider for getDefaultValue test
     * @return array
     */
    public function getDefaultValueDataProvider()
    {
        return [
            ['test', '', ''],
            ['ATTR:test', 'test', 'test'],
            ['ATTR:test:5', 'test.1,tes', 'select'],
        ];
    }
}
