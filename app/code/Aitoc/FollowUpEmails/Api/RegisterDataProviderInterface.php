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

/**
 * Interface RegistryDataProviderInterface
 */
interface RegisterDataProviderInterface
{
    const CURRENT_EVENT_CODE = 'aitoc_fue_current_event_code';
    const CURRENT_CAMPAIGN_ID = 'aitoc_fue_current_campaign_id';

    /**
     * @return string|null
     */
    public function getCurrentEventCode();

    /**
     * @param string $eventCode
     * @return self
     */
    public function setCurrentEventCode($eventCode);

    /**
     * @return null|int
     */
    public function getCurrentCampaignId();

    /**
     * @param $campaignId
     * @return self
     */
    public function setCurrentCampaignId($campaignId);
}
