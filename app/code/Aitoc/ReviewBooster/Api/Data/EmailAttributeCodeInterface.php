<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Data;

use Aitoc\FollowUpEmails\Api\Data\Source\EmailAttribute\EmailAttributeCodeInterface as FueEmailAttributeCodeInterface;

/**
 * Interface EmailAttributeCodeInterface
 */
interface EmailAttributeCodeInterface extends FueEmailAttributeCodeInterface
{
    const ORDER_ID = 'order_id';
    const ORDER_STATUS = 'order_status';
}
