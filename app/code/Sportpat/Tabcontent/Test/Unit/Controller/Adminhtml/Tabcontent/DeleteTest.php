<?php
namespace Sportpat\Tabcontent\Test\Unit\Controller\Adminhtml\Tabcontent;

use Sportpat\Tabcontent\Api\ExecutorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;
use Magento\Backend\App\Action\Context;
use Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Delete;

class DeleteTest extends TestCase
{
    /**
     * @var  Context | \PHPUnit_Framework_MockObject_MockObject
     */
    private $context;
    /**
     * @var ManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManager;
    /**
     * @var ResultFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactory;
    /**
     * @var Redirect | \PHPUnit_Framework_MockObject_MockObject
     */
    private $result;
    /**
     * @var RequestInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $request;
    /**
     * @var Delete
     */
    private $delete;
    /**
     * @var ExecutorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $executor;

    /**
     * setup tests
     */
    protected function setUp()
    {
        $this->context = $this->createMock(Context::class);
        $this->executor = $this->createMock(ExecutorInterface::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->result = $this->createMock(Redirect::class);
        $this->request = $this->createMock(RequestInterface::class);

        $this->context->method('getMessageManager')->willReturn($this->messageManager);
        $this->context->method('getResultFactory')->willReturn($this->resultFactory);
        $this->context->method('getRequest')->willReturn($this->request);
        $this->resultFactory->method('create')->willReturn($this->result);
        $this->delete = new Delete(
            $this->context,
            $this->executor,
            'id',
            'Success message',
            'Missing entity error',
            'General error message'
        );
    }

    /**
     * @covers \Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Delete::execute()
     */
    public function testExecute()
    {
        $this->request->method('getParam')->willReturn(1);
        $this->resultFactory->expects($this->once())->method('create');
        $this->executor->expects($this->once())->method('execute');
        $this->messageManager->expects($this->once())->method('addSuccessMessage');
        $this->messageManager->expects($this->never())->method('addErrorMessage');
        $this->assertSame($this->result, $this->delete->execute());
    }

    /**
     * @covers \Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Delete::execute() with no valid id
     */
    public function testExecuteNoId()
    {
        $this->request->method('getParam')->willReturn(null);
        $this->resultFactory->expects($this->once())->method('create');
        $this->executor->expects($this->never())->method('execute');
        $this->messageManager->expects($this->never())->method('addSuccessMessage');
        $this->messageManager->expects($this->once())->method('addErrorMessage');
        $this->assertSame($this->result, $this->delete->execute());
    }

    /**
     * @covers \Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Delete::execute() with no valid id
     */
    public function testExecuteNoSuchEntityException()
    {
        $this->request->method('getParam')->willReturn(null);
        $this->resultFactory->expects($this->once())->method('create');
        $this->executor->method('execute')
            ->willThrowException(new NoSuchEntityException(new Phrase('')));
        $this->messageManager->expects($this->never())->method('addSuccessMessage');
        $this->messageManager->expects($this->once())->method('addErrorMessage');
        $this->assertSame($this->result, $this->delete->execute());
    }

    /**
     * @covers \Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Delete::execute() with no valid id
     */
    public function testExecuteLocalizedException()
    {
        $this->request->method('getParam')->willReturn(null);
        $this->resultFactory->expects($this->once())->method('create');
        $this->executor->method('execute')
            ->willThrowException(new LocalizedException(new Phrase('')));
        $this->messageManager->expects($this->never())->method('addSuccessMessage');
        $this->messageManager->expects($this->once())->method('addErrorMessage');
        $this->assertSame($this->result, $this->delete->execute());
    }

    /**
     * @covers \Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent\Delete::execute() with no valid id
     */
    public function testExecuteException()
    {
        $this->request->method('getParam')->willReturn(null);
        $this->resultFactory->expects($this->once())->method('create');
        $this->executor->method('execute')
            ->willThrowException(new \Exception());
        $this->messageManager->expects($this->never())->method('addSuccessMessage');
        $this->messageManager->expects($this->once())->method('addErrorMessage');
        $this->assertSame($this->result, $this->delete->execute());
    }
}
