<?php
namespace Sportpat\OrderSync\Test\Unit\Controller\Adminhtml\Synced_order;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Title;
use PHPUnit\Framework\TestCase;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\App\Action\Context;
use Sportpat\OrderSync\Api\Synced_orderRepositoryInterface;
use Sportpat\OrderSync\Controller\Adminhtml\Synced_order\Edit;
use Magento\Framework\View\Page\Config;

class EditTest extends TestCase
{
    /**
     * @covers \Sportpat\OrderSync\Controller\Adminhtml\Synced_order\Edit::execute()
     */
    public function testExecute()
    {
        $resultFactory = $this->createMock(ResultFactory::class);
        $page = $this->createMock(Page::class);
        $resultFactory->method('create')->willReturn($page);
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('getParam');
        /** @var Synced_orderRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $syncedOrderRepository */
        $syncedOrderRepository = $this->createMock(Synced_orderRepositoryInterface::class);
        /** @var Registry | \PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->createMock(Registry::class);
        /** @var Context | \PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->createMock(Context::class);
        $context->method('getResultFactory')->willReturn($resultFactory);
        $context->method('getRequest')->willReturn($request);
        $page->expects($this->once())->method('setActiveMenu');
        /** @var Config | \PHPUnit_Framework_MockObject_MockObject $pageConfig */
        $pageConfig = $this->createMock(Config::class);
        $page->method('getConfig')->willReturn($pageConfig);
        $title = $this->createMock(Title::class);
        $title->expects($this->exactly(2))->method('prepend');
        $pageConfig->method('getTitle')->willReturn($title);
        $controller = new Edit($context, $syncedOrderRepository, $registry);
        $this->assertEquals($page, $controller->execute());
    }
}
