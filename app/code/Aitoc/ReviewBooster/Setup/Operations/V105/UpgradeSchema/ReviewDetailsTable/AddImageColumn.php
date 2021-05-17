<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V105\UpgradeSchema\ReviewDetailsTable;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Api\Setup\V100\ReviewDetailsTableInterface as ReviewDetailsTableInterfaceV100;
use Aitoc\ReviewBooster\Api\Setup\V105\ReviewDetailsTableInterface as ReviewDetailsTableInterfaceV105;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class AddImageColumn
 */
class AddImageColumn implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $reviewDetailsTableName = $setup->getTable(ReviewDetailsTableInterfaceV100::TABLE_NAME);
        $imageColumnName = ReviewDetailsTableInterfaceV105::COLUMN_NAME_IMAGE;

        $connection = $setup->getConnection();
        $connection->addColumn($reviewDetailsTableName, $imageColumnName, 'text');
    }
}
