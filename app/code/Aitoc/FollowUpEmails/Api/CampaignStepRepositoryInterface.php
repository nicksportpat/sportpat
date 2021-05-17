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

use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepSearchResultsInterface;

interface CampaignStepRepositoryInterface
{
    /**
     * @param CampaignStepInterface $campaignStepsModel
     * @return CampaignStepInterface
     */
    public function save(CampaignStepInterface $campaignStepsModel);

    /**
     * @param int $entityId
     * @return CampaignStepInterface
     */
    public function get($entityId);

    /**
     * @param CampaignStepInterface $campaignStepsModel
     * @return bool
     */
    public function delete(CampaignStepInterface $campaignStepsModel);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return CampaignStepSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
