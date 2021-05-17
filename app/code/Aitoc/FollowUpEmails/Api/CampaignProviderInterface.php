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

/**
 * Class CampaignProvider
 */
interface CampaignProviderInterface
{
    /**
     * @param string $eventCode
     * @return CampaignInterface[]
     */
    public function getCampaignsByEventCode($eventCode);
}
