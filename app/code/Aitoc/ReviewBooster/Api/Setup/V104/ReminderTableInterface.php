<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V104;

use Aitoc\ReviewBooster\Api\Setup\V103\ReminderTableInterface as ReminderTableInterfaceV103;

interface ReminderTableInterface extends ReminderTableInterfaceV103
{
    const COLUMN_NAME_UNSUBSCRIBE_CODE = 'unsubscribe_code';
}
