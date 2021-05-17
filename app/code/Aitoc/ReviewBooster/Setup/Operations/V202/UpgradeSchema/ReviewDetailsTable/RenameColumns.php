<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Created by PhpStorm.
 * User: Likemusic
 * Date: 28.01.2019
 * Time: 23:44
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V202\UpgradeSchema\ReviewDetailsTable;

use Aitoc\FollowUpEmails\Setup\Base\RenameColumns as BaseRenameColumns;
use Aitoc\ReviewBooster\Api\Setup\V200\ReviewDetailsTableInterface as ReviewDetailsTableV200Interface;
use Aitoc\ReviewBooster\Api\Setup\V202\ReviewDetailsTableInterface as ReviewDetailsTableV202Interface;

/**
 * Class RenameColumns
 */
class RenameColumns extends BaseRenameColumns
{
    /**
     * @return string
     */
    protected function getTableName()
    {
        return ReviewDetailsTableV202Interface::TABLE_NAME;
    }

    protected function getRenameColumnsMap()
    {
        return [
            ReviewDetailsTableV200Interface::COLUMN_NAME_PRODUCT_ADVANTAGES
                => ReviewDetailsTableV202Interface::COLUMN_NAME_PRODUCT_ADVANTAGES,

            ReviewDetailsTableV200Interface::COLUMN_NAME_PRODUCT_DISADVANTAGES
            => ReviewDetailsTableV202Interface::COLUMN_NAME_PRODUCT_DISADVANTAGES,

            ReviewDetailsTableV200Interface::COLUMN_NAME_REVIEW_HELPFUL
            => ReviewDetailsTableV202Interface::COLUMN_NAME_REVIEW_HELPFUL,

            ReviewDetailsTableV200Interface::COLUMN_NAME_REVIEW_UNHELPFUL
            => ReviewDetailsTableV202Interface::COLUMN_NAME_REVIEW_UNHELPFUL,

            ReviewDetailsTableV200Interface::COLUMN_NAME_REVIEW_REPORTED
            => ReviewDetailsTableV202Interface::COLUMN_NAME_REVIEW_REPORTED,

            ReviewDetailsTableV200Interface::COLUMN_NAME_CUSTOMER_VERIFIED
            => ReviewDetailsTableV202Interface::COLUMN_NAME_CUSTOMER_VERIFIED,
        ];
    }
}
