<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V103;

use Aitoc\ReviewBooster\Api\Setup\V102\ReminderTableInterface as ReminderTableInterfaceV102;

interface ReminderTableInterface extends ReminderTableInterfaceV102
{
    const COLUMN_NAME_SALES_RULE_ID = 'sales_rule_id';
}
