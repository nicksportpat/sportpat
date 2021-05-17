<?php
namespace Sportpat\HomeBanner\Test\Unit\Model\Banner\Executor;

use PHPUnit\Framework\TestCase;
use Sportpat\HomeBanner\Api\BannerRepositoryInterface;
use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Sportpat\HomeBanner\Model\Banner\Executor\Delete;

class DeleteTest extends TestCase
{
    /**
     * @covers \Sportpat\HomeBanner\Model\Banner\Executor\Delete::execute()
     */
    public function testExecute()
    {
        /** @var BannerRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $bannerRepository */
        $bannerRepository = $this->createMock(BannerRepositoryInterface::class);
        $bannerRepository->expects($this->once())->method('deleteById');
        /** @var BannerInterface | \PHPUnit_Framework_MockObject_MockObject $banner */
        $banner = $this->createMock(BannerInterface::class);
        $delete = new Delete($bannerRepository);
        $delete->execute($banner->getId());
    }
}
