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
 * Interface CampaignsTable
 */
interface CampaignTableInterface
{
    const TABLE_NAME = 'aitoc_follow_up_emails_campaigns';

    const COLUMN_NAME_ENTITY_ID = 'entity_id';
    const COLUMN_NAME_NAME = 'name';
    const COLUMN_NAME_DESCRIPTION = 'description';
    const COLUMN_NAME_EVENT_CODE = 'event_code';
    const COLUMN_NAME_SENDER = 'sender';
    const COLUMN_NAME_STATUS = 'status';
    const COLUMN_NAME_CUSTOMER_GROUP_IDS = 'customer_group_ids';
    const COLUMN_NAME_STORE_IDS = 'store_ids';
}
