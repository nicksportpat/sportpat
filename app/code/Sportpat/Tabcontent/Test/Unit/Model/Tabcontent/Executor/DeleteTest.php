<?php
namespace Sportpat\Tabcontent\Test\Unit\Model\Tabcontent\Executor;

use PHPUnit\Framework\TestCase;
use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;
use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Sportpat\Tabcontent\Model\Tabcontent\Executor\Delete;

class DeleteTest extends TestCase
{
    /**
     * @covers \Sportpat\Tabcontent\Model\Tabcontent\Executor\Delete::execute()
     */
    public function testExecute()
    {
        /** @var TabcontentRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $tabcontentRepository */
        $tabcontentRepository = $this->createMock(TabcontentRepositoryInterface::class);
        $tabcontentRepository->expects($this->once())->method('deleteById');
        /** @var TabcontentInterface | \PHPUnit_Framework_MockObject_MockObject $tabcontent */
        $tabcontent = $this->createMock(TabcontentInterface::class);
        $delete = new Delete($tabcontentRepository);
        $delete->execute($tabcontent->getId());
    }
}
