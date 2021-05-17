<?php
namespace Sportpat\Tabcontent\Api;

use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface TabcontentRepositoryInterface
{
    /**
     * @param TabcontentInterface $tabcontent
     * @return TabcontentInterface
     */
    public function save(TabcontentInterface $tabcontent);

    /**
     * @param $id
     * @return TabcontentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Sportpat\Tabcontent\Api\Data\TabcontentSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param TabcontentInterface $tabcontent
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(TabcontentInterface $tabcontent);

    /**
     * @param int $tabcontentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tabcontentId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();
}
