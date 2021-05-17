<?php
namespace Sportpat\OrderSync\Test\Unit\Block\Adminhtml\Button\Synced_order;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Sportpat\OrderSync\Api\Data\Synced_orderInterface;
use Sportpat\OrderSync\Block\Adminhtml\Button\Synced_order\Delete;

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
     * @covers \Sportpat\OrderSync\Block\Adminhtml\Button\Synced_order\Delete::getButtonData()
     */
    public function testButtonDataNoSynced_order()
    {
        $this->registry->method('registry')->willReturn(null);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Sportpat\OrderSync\Block\Adminhtml\Button\Synced_order\Delete::getButtonData()
     */
    public function testButtonDataNoSynced_orderId()
    {
        $syncedOrder = $this->createMock(Synced_orderInterface::class);
        $syncedOrder->method('getId')->willReturn(null);
        $this->registry->method('registry')->willReturn($syncedOrder);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Sportpat\OrderSync\Block\Adminhtml\Button\Synced_order\Delete::getButtonData()
     */
    public function testButtonData()
    {
        $syncedOrder = $this->createMock(Synced_orderInterface::class);
        $syncedOrder->method('getId')->willReturn(2);
        $this->registry->method('registry')->willReturn($syncedOrder);
        $this->url->expects($this->once())->method('getUrl');
        $data = $this->button->getButtonData();
        $this->assertArrayHasKey('on_click', $data);
        $this->assertArrayHasKey('label', $data);
        $this->assertArrayHasKey('class', $data);
    }
}
