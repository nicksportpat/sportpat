<?php
namespace Sportpat\OrderSync\Test\Unit\Model\Syncedorder\Executor;

use PHPUnit\Framework\TestCase;
use Sportpat\OrderSync\Api\SyncedorderRepositoryInterface;
use Sportpat\OrderSync\Api\Data\SyncedorderInterface;
use Sportpat\OrderSync\Model\Syncedorder\Executor\Delete;

class DeleteTest extends TestCase
{
    /**
     * @covers \Sportpat\OrderSync\Model\Syncedorder\Executor\Delete::execute()
     */
    public function testExecute()
    {
        /** @var SyncedorderRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $syncedorderRepository */
        $syncedorderRepository = $this->createMock(SyncedorderRepositoryInterface::class);
        $syncedorderRepository->expects($this->once())->method('deleteById');
        /** @var SyncedorderInterface | \PHPUnit_Framework_MockObject_MockObject $syncedorder */
        $syncedorder = $this->createMock(SyncedorderInterface::class);
        $delete = new Delete($syncedorderRepository);
        $delete->execute($syncedorder->getId());
    }
}
