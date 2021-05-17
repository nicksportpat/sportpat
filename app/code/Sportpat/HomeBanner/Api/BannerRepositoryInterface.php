<?php
namespace Sportpat\HomeBanner\Api;

use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * @param BannerInterface $banner
     * @return BannerInterface
     */
    public function save(BannerInterface $banner);

    /**
     * @param $id
     * @return BannerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Sportpat\HomeBanner\Api\Data\BannerSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param BannerInterface $banner
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BannerInterface $banner);

    /**
     * @param int $bannerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bannerId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();
}
