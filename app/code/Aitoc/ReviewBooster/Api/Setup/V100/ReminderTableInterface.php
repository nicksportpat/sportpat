<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V100;

interface ReminderTableInterface
{
    const TABLE_NAME = 'aitoc_review_booster_reminder';

    const COLUMN_NAME_ID = 'reminder_id';
    const COLUMN_NAME_STORE_ID = 'store_id';
    const COLUMN_NAME_CUSTOMER_ID = 'customer_id';
    const COLUMN_NAME_ORDER_ID = 'order_id';
    const COLUMN_NAME_STATUS = 'status';
    const COLUMN_NAME_CREATED_AT = 'created_at';
    const COLUMN_NAME_SENT_AT = 'sent_at';
    const COLUMN_NAME_CUSTOMER_NAME = 'customer_name';
    const COLUMN_NAME_CUSTOMER_EMAIL = 'customer_email';
    const COLUMN_NAME_PRODUCTS = 'products';
}
