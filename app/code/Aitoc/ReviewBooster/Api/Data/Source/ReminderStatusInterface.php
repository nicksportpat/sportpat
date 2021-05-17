<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Data\Source;

interface ReminderStatusInterface
{
    const PENDING = 'pending';
    const SENT = 'sent';
    const FAILED = 'failed';
}
