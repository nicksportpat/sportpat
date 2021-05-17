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

use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterface;

interface CampaignRepositoryInterface
{
    /**
     * @param CampaignInterface $campaignsModel
     * @return CampaignInterface
     */
    public function save(CampaignInterface $campaignsModel);

    /**
     * @param int $entityId
     * @return CampaignInterface
     */
    public function get($entityId);

    /**
     * @param CampaignInterface $campaignsModel
     * @return bool
     */
    public function delete(CampaignInterface $campaignsModel);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return CampaignSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
