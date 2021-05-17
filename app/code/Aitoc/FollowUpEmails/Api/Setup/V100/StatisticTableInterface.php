<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Setup\V100;

/**
 * Interface StatisticTableInterface
 */
interface StatisticTableInterface
{
    const TABLE_NAME = 'aitoc_follow_up_emails_statistics';

    const COLUMN_NAME_ENTITY_ID = 'entity_id';
    const COLUMN_NAME_CAMPAIGN_ID = 'campaign_id';
    const COLUMN_NAME_CAMPAIGN_STEP_ID = 'campaign_step_id';
    const COLUMN_NAME_KEY = 'key';
    const COLUMN_NAME_VALUE = 'value';
    const COLUMN_NAME_UPDATED_AT = 'updated_at';
    const COLUMN_NAME_EVENT_CODE = 'event_code';
}
