<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Api\Setup\V202;

use Aitoc\ReviewBooster\Api\Setup\V200\ReviewDetailsTableInterface as ReviewDetailsTableV200Interface;

/**
 * Interface ReviewDetailsTableInterface
 */
interface ReviewDetailsTableInterface
{
    const TABLE_NAME = ReviewDetailsTableV200Interface::TABLE_NAME;
    const COLUMN_NAME_ID = ReviewDetailsTableV200Interface::COLUMN_NAME_ID;
    const COLUMN_NAME_REVIEW_ID = ReviewDetailsTableV200Interface::COLUMN_NAME_REVIEW_ID;
    const COLUMN_NAME_COMMENT = ReviewDetailsTableV200Interface::COLUMN_NAME_COMMENT;
    const COLUMN_NAME_ADMIN_TITLE = ReviewDetailsTableV200Interface::COLUMN_NAME_ADMIN_TITLE;

    /* Renamed columns */
    const COLUMN_NAME_PRODUCT_ADVANTAGES = 'product_advantages';
    const COLUMN_NAME_PRODUCT_DISADVANTAGES = 'product_disadvantages';
    const COLUMN_NAME_REVIEW_HELPFUL = 'review_helpful';
    const COLUMN_NAME_REVIEW_UNHELPFUL = 'review_unhelpful';
    const COLUMN_NAME_REVIEW_REPORTED = 'review_reported';
    const COLUMN_NAME_CUSTOMER_VERIFIED = 'customer_verified';
}
