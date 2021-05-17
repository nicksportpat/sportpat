<?php
namespace Sportpat\HomeBanner\Test\Unit\Block\Adminhtml\Button\Banner;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Sportpat\HomeBanner\Block\Adminhtml\Button\Banner\Delete;

class DeleteTest extends TestCase
{
    /**
     * @var UrlInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $url;
    /**
     * @var Registry | \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;
    /**
     * @var Delete
     */
    private $button;

    /**
     * set up tests
     */
    protected function setUp()
    {
        $this->url = $this->createMock(UrlInterface::class);
        $this->registry = $this->createMock(Registry::class);
        $this->button = new Delete($this->registry, $this->url);
    }

    /**
     * @covers \Sportpat\HomeBanner\Block\Adminhtml\Button\Banner\Delete::getButtonData()
     */
    public function testButtonDataNoBanner()
    {
        $this->registry->method('registry')->willReturn(null);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Sportpat\HomeBanner\Block\Adminhtml\Button\Banner\Delete::getButtonData()
     */
    public function testButtonDataNoBannerId()
    {
        $banner = $this->createMock(BannerInterface::class);
        $banner->method('getId')->willReturn(null);
        $this->registry->method('registry')->willReturn($banner);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Sportpat\HomeBanner\Block\Adminhtml\Button\Banner\Delete::getButtonData()
     */
    public function testButtonData()
    {
        $banner = $this->createMock(BannerInterface::class);
        $banner->method('getId')->willReturn(2);
        $this->registry->method('registry')->willReturn($banner);
        $this->url->expects($this->once())->method('getUrl');
        $data = $this->button->getButtonData();
        $this->assertArrayHasKey('on_click', $data);
        $this->assertArrayHasKey('label', $data);
        $this->assertArrayHasKey('class', $data);
    }
}
