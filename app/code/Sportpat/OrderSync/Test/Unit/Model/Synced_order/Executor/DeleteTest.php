<?php
namespace Sportpat\OrderSync\Test\Unit\Model\Synced_order\Executor;

use PHPUnit\Framework\TestCase;
use Sportpat\OrderSync\Api\Synced_orderRepositoryInterface;
use Sportpat\OrderSync\Api\Data\Synced_orderInterface;
use Sportpat\OrderSync\Model\Synced_order\Executor\Delete;

class DeleteTest extends TestCase
{
    /**
     * @covers \Sportpat\OrderSync\Model\Synced_order\Executor\Delete::execute()
     */
    public function testExecute()
    {
        /** @var Synced_orderRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $syncedOrderRepository */
        $syncedOrderRepository = $this->createMock(Synced_orderRepositoryInterface::class);
        $syncedOrderRepository->expects($this->once())->method('deleteById');
        /** @var Synced_orderInterface | \PHPUnit_Framework_MockObject_MockObject $syncedOrder */
        $syncedOrder = $this->createMock(Synced_orderInterface::class);
        $delete = new Delete($syncedOrderRepository);
        $delete->execute($syncedOrder->getId());
    }
}
