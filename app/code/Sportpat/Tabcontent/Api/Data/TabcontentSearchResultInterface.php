<?php
namespace Sportpat\Tabcontent\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface TabcontentSearchResultInterface
{
    /**
     * get items
     *
     * @return \Sportpat\Tabcontent\Api\Data\TabcontentInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Sportpat\Tabcontent\Api\Data\TabcontentInterface[] $items
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
