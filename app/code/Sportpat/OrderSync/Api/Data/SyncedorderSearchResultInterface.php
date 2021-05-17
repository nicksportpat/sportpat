<?php
namespace Sportpat\OrderSync\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface SyncedorderSearchResultInterface
{
    /**
     * get items
     *
     * @return \Sportpat\OrderSync\Api\Data\SyncedorderInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Sportpat\OrderSync\Api\Data\SyncedorderInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);
}
