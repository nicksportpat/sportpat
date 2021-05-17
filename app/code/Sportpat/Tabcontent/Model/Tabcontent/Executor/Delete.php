<?php
namespace Sportpat\Tabcontent\Model\Tabcontent\Executor;

use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;
use Sportpat\Tabcontent\Api\ExecutorInterface;

class Delete implements ExecutorInterface
{
    /**
     * @var TabcontentRepositoryInterface
     */
    private $tabcontentRepository;

    /**
     * Delete constructor.
     * @param TabcontentRepositoryInterface $tabcontentRepository
     */
    public function __construct(
        TabcontentRepositoryInterface $tabcontentRepository
    ) {
        $this->tabcontentRepository = $tabcontentRepository;
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id)
    {
        $this->tabcontentRepository->deleteById($id);
    }
}
