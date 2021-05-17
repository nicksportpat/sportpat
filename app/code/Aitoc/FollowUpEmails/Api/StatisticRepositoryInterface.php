<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Api;

use Aitoc\FollowUpEmails\Api\Data\StatisticInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aitoc\FollowUpEmails\Api\Data\StatisticSearchResultsInterface;

interface StatisticRepositoryInterface
{
    /**
     * @param StatisticInterface $statisticsModel
     * @return StatisticInterface
     */
    public function save(StatisticInterface $statisticsModel);

    /**
     * @param int $entityId
     * @return StatisticInterface
     */
    public function get($entityId);

    /**
     * @param StatisticInterface $statisticsModel
     * @return bool
     */
    public function delete(StatisticInterface $statisticsModel);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return StatisticSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
