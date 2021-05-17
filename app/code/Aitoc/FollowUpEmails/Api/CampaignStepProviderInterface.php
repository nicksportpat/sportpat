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
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;

/**
 * Class CampaignStepsProvider
 */
interface CampaignStepProviderInterface
{
    /**
     * @param CampaignInterface $campaign
     * @return CampaignStepInterface[]
     */
    public function getCampaignStepsByCampaign(CampaignInterface $campaign);

    /**
     * @param int $campaignId
     * @return CampaignStepInterface[]
     */
    public function getCampaignStepsByCampaignId($campaignId);
}
