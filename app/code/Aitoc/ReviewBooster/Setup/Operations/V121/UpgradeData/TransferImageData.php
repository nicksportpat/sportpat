<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V121\UpgradeData;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\ReviewBooster\Api\Setup\V111\ReviewDetailsTableInterface;
use Aitoc\ReviewBooster\Api\Setup\V121\ReviewImageTableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class TransferImageData
 */
class TransferImageData implements UpgradeDataOperationInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $imageTableName = $setup->getTable(ReviewImageTableInterface::TABLE_NAME);
        $reviewDetailsTableName = $setup->getTable(ReviewDetailsTableInterface::TABLE_NAME);

        $connection = $setup->getConnection();

        $sql = "INSERT INTO {$imageTableName} (review_id, image)
                    SELECT review_id, image FROM {$reviewDetailsTableName}
                    WHERE image IS NOT NULL";

        $connection->query($sql);

        $connection->dropColumn(
            $reviewDetailsTableName,
            ReviewDetailsTableInterface::COLUMN_NAME_IMAGE
        );
    }
}
