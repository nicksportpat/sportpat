<?php
namespace Sportpat\OrderSync\Model\Syncedorder\Executor;

use Sportpat\OrderSync\Api\SyncedorderRepositoryInterface;
use Sportpat\OrderSync\Api\ExecutorInterface;

class Delete implements ExecutorInterface
{
    /**
     * @var SyncedorderRepositoryInterface
     */
    private $syncedorderRepository;

    /**
     * Delete constructor.
     * @param SyncedorderRepositoryInterface $syncedorderRepository
     */
    public function __construct(
        SyncedorderRepositoryInterface $syncedorderRepository
    ) {
        $this->syncedorderRepository = $syncedorderRepository;
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id)
    {
        $this->syncedorderRepository->deleteById($id);
    }
}
