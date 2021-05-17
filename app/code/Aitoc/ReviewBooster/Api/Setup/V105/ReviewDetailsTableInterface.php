<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V105;

use Aitoc\ReviewBooster\Api\Setup\V104\ReviewDetailsTableInterface as ReviewDetailsTableInterfaceV104;

interface ReviewDetailsTableInterface extends ReviewDetailsTableInterfaceV104
{
    const COLUMN_NAME_IMAGE = 'image';
}
