<?php
namespace Sportpat\HomeBanner\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface BannerSearchResultInterface
{
    /**
     * get items
     *
     * @return \Sportpat\HomeBanner\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Sportpat\HomeBanner\Api\Data\BannerInterface[] $items
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
