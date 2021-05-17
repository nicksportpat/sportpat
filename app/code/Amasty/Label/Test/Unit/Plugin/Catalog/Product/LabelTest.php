<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Plugin\Catalog\Product;

use Amasty\Label\Plugin\Catalog\Product\Label;
use Amasty\Label\Test\Unit\Traits;
use Magento\Catalog\Model\Product;

/**
 * Class LabelTest
 *
 * @see Label
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class LabelTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Label::afterToHtml
     */
    public function testAfterToHtml()
    {
        $plugin = $this->createPartialMock(Label::class, []);
        $product = $this->createPartialMock(Product::class, []);
        $registry = $this->createPartialMock(\Magento\Framework\Registry::class, []);
        $request = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getModuleName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $helper = $this->createPartialMock(\Amasty\Label\Model\LabelViewer::class, ['renderProductLabel']);
        $subject = $this->createPartialMock(\Magento\Catalog\Block\Product\Image::class, ['getProduct']);

        $subject->expects($this->any())->method('getProduct')->willReturn($product);
        $request->expects($this->any())->method('getModuleName')->willReturnOnConsecutiveCalls('checkout', '');
        $helper->expects($this->once())->method('renderProductLabel')->willReturn('test');

        $this->setProperty($plugin, 'registry', $registry, Label::class);
        $this->setProperty($plugin, 'request', $request, Label::class);
        $this->setProperty($plugin, 'helper', $helper, Label::class);

        $this->assertEquals('test', $plugin->afterToHtml($subject, 'test'));
        $this->assertEquals('testtest', $plugin->afterToHtml($subject, 'test'));
    }
}
