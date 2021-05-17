<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V111;

use Aitoc\ReviewBooster\Api\Setup\V105\ReviewDetailsTableInterface as ReviewDetailsTableV105Interface;

interface ReviewDetailsTableInterface extends ReviewDetailsTableV105Interface
{
    const COLUMN_NAME_COMMENT = 'comment';
    const COLUMN_NAME_ADMIN_TITLE = 'admin_title';
}
