<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V121;

use Aitoc\ReviewBooster\Api\Setup\V111\ReviewDetailsTableInterface as ReviewDetailsTableInterfaceV111;

interface ReviewDetailsTableInterface
{
    //to remove `const COLUMN_NAME_IMAGE = 'image';` copy all except excluded
    const TABLE_NAME = ReviewDetailsTableInterfaceV111::TABLE_NAME;

    const COLUMN_NAME_ID = ReviewDetailsTableInterfaceV111::COLUMN_NAME_ID;

    const COLUMN_NAME_REVIEW_ID = ReviewDetailsTableInterfaceV111::COLUMN_NAME_REVIEW_ID;
    const COLUMN_NAME_PRODUCT_ADVANTAGES = ReviewDetailsTableInterfaceV111::COLUMN_NAME_PRODUCT_ADVANTAGES;
    const COLUMN_NAME_PRODUCT_DISADVANTAGES = ReviewDetailsTableInterfaceV111::COLUMN_NAME_PRODUCT_DISADVANTAGES;
    const COLUMN_NAME_REVIEW_HELPFUL = ReviewDetailsTableInterfaceV111::COLUMN_NAME_REVIEW_HELPFUL;
    const COLUMN_NAME_REVIEW_UNHELPFUL = ReviewDetailsTableInterfaceV111::COLUMN_NAME_REVIEW_UNHELPFUL;
    const COLUMN_NAME_REVIEW_REPORTED = ReviewDetailsTableInterfaceV111::COLUMN_NAME_REVIEW_REPORTED;
    const COLUMN_NAME_CUSTOMER_VERIFIED = ReviewDetailsTableInterfaceV111::COLUMN_NAME_CUSTOMER_VERIFIED;

    const COLUMN_NAME_COMMENT = ReviewDetailsTableInterfaceV111::COLUMN_NAME_COMMENT;
    const COLUMN_NAME_ADMIN_TITLE = ReviewDetailsTableInterfaceV111::COLUMN_NAME_ADMIN_TITLE;
}
