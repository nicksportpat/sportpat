<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Api\Data\Source\Email;

/**
 * Interface EmailStatusesInterface
 */
interface StatusInterface
{
    /**
     * Constants defined for email statuses
     */
    const STATUS_PENDING = 1;
    const STATUS_SENT = 2;
    const STATUS_HOLD = 3;
    const STATUS_ERROR = 4;
}
