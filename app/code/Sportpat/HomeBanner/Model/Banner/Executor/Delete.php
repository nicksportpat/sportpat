<?php
namespace Sportpat\HomeBanner\Model\Banner\Executor;

use Sportpat\HomeBanner\Api\BannerRepositoryInterface;
use Sportpat\HomeBanner\Api\ExecutorInterface;

class Delete implements ExecutorInterface
{
    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * Delete constructor.
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository
    ) {
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id)
    {
        $this->bannerRepository->deleteById($id);
    }
}
