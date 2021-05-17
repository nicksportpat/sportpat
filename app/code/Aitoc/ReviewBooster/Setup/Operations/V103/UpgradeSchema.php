<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V103;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V103\UpgradeSchema\ReminderTable as ReminderTableOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaOperationInterface
{
    /**
     * @var ReminderTableOperation
     */
    private $reminderTableOperation;

    /**
     * UpgradeSchema constructor.
     * @param ReminderTableOperation $reminderTableOperation
     */
    public function __construct(ReminderTableOperation $reminderTableOperation)
    {
        $this->reminderTableOperation = $reminderTableOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->upgradeReminderTableSchema($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeReminderTableSchema(SchemaSetupInterface $setup)
    {
        $this->reminderTableOperation->execute($setup);
    }
}
