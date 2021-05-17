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
 * Interface UnsubscribedListTableInterface
 */
interface UnsubscribedEmailAddressTableInterface
{
    const TABLE_NAME = 'aitoc_follow_up_emails_unsubscribed_list';

    const COLUMN_NAME_ENTITY_ID = 'entity_id';
    const COLUMN_NAME_CUSTOMER_EMAIL = 'customer_email';
    const COLUMN_NAME_CREATED_AT = 'created_at';
    const COLUMN_NAME_EVENT_CODE = 'event_code';
    const COLUMN_NAME_EMAIL_ID = 'email_id';
}
