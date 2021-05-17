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
 * Interface MagentoReviewInterface
 */
interface ReviewInterface
{
    const TABLE_NAME = 'review';

    const COLUMN_NAME_REVIEW_ID = 'review_id';
    const COLUMN_NAME_CREATED_AT = 'created_at';
    const COLUMN_NAME_ENTITY_ID = 'entity_id';
    const COLUMN_NAME_ENTITY_PK_VALUE = 'entity_pk_value';
    const COLUMN_NAME_STATUS_ID = 'status_id';

    const COLUMN_NAME_CUSTOMER_ID = 'customer_id';
}
