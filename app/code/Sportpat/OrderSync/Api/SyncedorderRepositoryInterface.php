<?php
namespace Sportpat\OrderSync\Api;

use Sportpat\OrderSync\Api\Data\SyncedorderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface SyncedorderRepositoryInterface
{
    /**
     * @param SyncedorderInterface $syncedorder
     * @return SyncedorderInterface
     */
    public function save(SyncedorderInterface $syncedorder);

    /**
     * @param $id
     * @return SyncedorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Sportpat\OrderSync\Api\Data\SyncedorderSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param SyncedorderInterface $syncedorder
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(SyncedorderInterface $syncedorder);

    /**
     * @param int $syncedorderId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($syncedorderId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();
}
