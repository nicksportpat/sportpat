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
 * Interface EmailTableInterface
 */
interface EmailTableInterface
{
    const TABLE_NAME = 'aitoc_follow_up_emails_emails';

    /**
     * Constants defined for keys of data array
     */
    const COLUMN_NAME_ENTITY_ID = 'entity_id';
    const COLUMN_NAME_CREATED_AT = 'created_at';
    const COLUMN_NAME_SCHEDULED_AT = 'scheduled_at';
    const COLUMN_NAME_SENT_AT = 'sent_at';
    const COLUMN_NAME_OPENED_AT = 'opened_at';
    const COLUMN_NAME_TRANSITED_AT = 'transited_at';
    const COLUMN_NAME_CAMPAIGN_STEP_ID = 'campaign_step_id';
    const COLUMN_NAME_CUSTOMER_EMAIL = 'customer_email';
    const COLUMN_NAME_STATUS = 'status';
    const COLUMN_NAME_SECRET_CODE = 'unsubscribe_code';//todo: rename in table column in update schema script
    const COLUMN_NAME_EMAIL_ATTRIBUTES = 'email_attributes';
}
