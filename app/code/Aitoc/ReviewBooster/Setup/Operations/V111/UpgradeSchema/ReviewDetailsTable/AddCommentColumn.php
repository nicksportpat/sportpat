<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V111\UpgradeSchema\ReviewDetailsTable;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Api\Setup\V105\ReviewDetailsTableInterface as ReviewDetailsTableV105Interface;
use Aitoc\ReviewBooster\Api\Setup\V111\ReviewDetailsTableInterface as ReviewDetailsTableV111Interface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddCommentColumn
 */
class AddCommentColumn implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $reviewDetailsTableName = $setup->getTable(ReviewDetailsTableV105Interface::TABLE_NAME);
        $commentColumnName = ReviewDetailsTableV111Interface::COLUMN_NAME_COMMENT;
        $connection = $setup->getConnection();

        $connection->addColumn($reviewDetailsTableName, $commentColumnName, 'text');
    }
}
