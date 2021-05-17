<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Data\Source\Table;

/**
 * Interface ReviewDetail
 */
interface ReviewDetailInterface
{
    const TABLE_NAME = 'review_detail';

    const COLUMN_NAME_DETAIL_ID = 'detail_id';
    const COLUMN_NAME_REVIEW_ID = 'review_id';
    const COLUMN_NAME_STORE_ID = 'store_id';
    const COLUMN_NAME_TITLE = 'title';
    const COLUMN_NAME_DETAIL = 'detail';
    const COLUMN_NAME_NICKNAME = 'nickname';
    const COLUMN_NAME_CUSTOMER_ID = 'customer_id';
}
