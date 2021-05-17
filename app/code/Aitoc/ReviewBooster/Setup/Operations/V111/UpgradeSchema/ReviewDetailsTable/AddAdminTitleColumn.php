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
 * Class AddAdminTitleColumn
 */
class AddAdminTitleColumn implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $reviewDetailsTable = $setup->getTable(ReviewDetailsTableV105Interface::TABLE_NAME);
        $adminTitleColumnName = ReviewDetailsTableV111Interface::COLUMN_NAME_ADMIN_TITLE;
        $connection = $setup->getConnection();

        $connection->addColumn($reviewDetailsTable, $adminTitleColumnName, 'text');
    }
}
