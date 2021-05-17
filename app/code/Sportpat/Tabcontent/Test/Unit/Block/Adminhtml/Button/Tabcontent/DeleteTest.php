<?php
namespace Sportpat\Tabcontent\Test\Unit\Block\Adminhtml\Button\Tabcontent;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Sportpat\Tabcontent\Block\Adminhtml\Button\Tabcontent\Delete;

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
     * @covers \Sportpat\Tabcontent\Block\Adminhtml\Button\Tabcontent\Delete::getButtonData()
     */
    public function testButtonDataNoTabcontent()
    {
        $this->registry->method('registry')->willReturn(null);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Sportpat\Tabcontent\Block\Adminhtml\Button\Tabcontent\Delete::getButtonData()
     */
    public function testButtonDataNoTabcontentId()
    {
        $tabcontent = $this->createMock(TabcontentInterface::class);
        $tabcontent->method('getId')->willReturn(null);
        $this->registry->method('registry')->willReturn($tabcontent);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Sportpat\Tabcontent\Block\Adminhtml\Button\Tabcontent\Delete::getButtonData()
     */
    public function testButtonData()
    {
        $tabcontent = $this->createMock(TabcontentInterface::class);
        $tabcontent->method('getId')->willReturn(2);
        $this->registry->method('registry')->willReturn($tabcontent);
        $this->url->expects($this->once())->method('getUrl');
        $data = $this->button->getButtonData();
        $this->assertArrayHasKey('on_click', $data);
        $this->assertArrayHasKey('label', $data);
        $this->assertArrayHasKey('class', $data);
    }
}
