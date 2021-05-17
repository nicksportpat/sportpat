<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Plugin\Catalog\Product;

use Amasty\Label\Plugin\Catalog\Product\ListProduct;
use Amasty\Label\Test\Unit\Traits;
use Magento\Catalog\Model\Product;

/**
 * Class ListProductTest
 *
 * @see ListProduct
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ListProductTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers ListProduct::afterToHtml
     */
    public function testAfterToHtml()
    {
        $plugin = $this->createPartialMock(ListProduct::class, []);
        $registry = $this->createPartialMock(\Magento\Framework\Registry::class, ['registry']);
        $helper = $this->createPartialMock(\Amasty\Label\Model\LabelViewer::class, ['renderProductLabel']);
        $subject = $this->createPartialMock(
            \Magento\Catalog\Block\Product\Image::class,
            ['getLoadedProductCollection', 'getProductCollection']
        );
        $product1 = $this->createPartialMock(Product::class, []);
        $product2 = $this->createPartialMock(Product::class, []);

        $registry->expects($this->any())->method('registry')->willReturnOnConsecutiveCalls(true, false);
        $subject->expects($this->any())->method('getProductCollection')->willReturn([$product1, $product2]);
        $helper->expects($this->any())->method('renderProductLabel')->willReturn('test');

        $this->setProperty($plugin, 'registry', $registry, ListProduct::class);
        $this->setProperty($plugin, 'helper', $helper, ListProduct::class);

        $this->assertEquals('test', $plugin->afterToHtml($subject, 'test'));
        $this->assertEquals('testtesttest', $plugin->afterToHtml($subject, 'test'));
    }
}
