<?php
namespace Sportpat\Tabcontent\Test\Unit\Controller\Adminhtml\Tabcontent;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Title;
use PHPUnit\Framework\TestCase;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\App\Action\Context;
use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;
use Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Edit;
use Magento\Framework\View\Page\Config;

class EditTest extends TestCase
{
    /**
     * @covers \Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Edit::execute()
     */
    public function testExecute()
    {
        $resultFactory = $this->createMock(ResultFactory::class);
        $page = $this->createMock(Page::class);
        $resultFactory->method('create')->willReturn($page);
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->once())->method('getParam');
        /** @var TabcontentRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $tabcontentRepository */
        $tabcontentRepository = $this->createMock(TabcontentRepositoryInterface::class);
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
        $controller = new Edit($context, $tabcontentRepository, $registry);
        $this->assertEquals($page, $controller->execute());
    }
}
