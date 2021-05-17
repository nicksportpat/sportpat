<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Setup\Operations\V102\UpgradeSchema\Email;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Aitoc\FollowUpEmails\Api\Setup\V102\EmailTableInterface as EmailTableV102Interface;

/**
 * Class AlertScheduledAt
 */
class AlterScheduledAt implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $dbAdapter = $setup->getConnection();

        $prefixedEmailTableName = $setup->getTable(EmailTableV102Interface::TABLE_NAME);

        $dbAdapter->modifyColumn(
            $prefixedEmailTableName,
            EmailTableV102Interface::COLUMN_NAME_SCHEDULED_AT,
            [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'default' => null,
                'comment' => 'Scheduled at',
            ]
        );
    }
}
