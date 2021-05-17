<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V103\UpgradeSchema;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V103\UpgradeSchema\ReminderTable\AddSalesRuleIdColumn as AddSalesRuleIdColumnOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class ReminderTable
 */
class ReminderTable implements UpgradeSchemaOperationInterface
{
    /**
     * @var AddSalesRuleIdColumnOperation
     */
    private $addSalesRuleIdColumnOperation;

    /**
     * ReminderTable constructor.
     * @param AddSalesRuleIdColumnOperation $addSalesRuleIdColumnOperation
     */
    public function __construct(AddSalesRuleIdColumnOperation $addSalesRuleIdColumnOperation)
    {
        $this->addSalesRuleIdColumnOperation = $addSalesRuleIdColumnOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addSalesRuleIdColumn($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addSalesRuleIdColumn(SchemaSetupInterface $setup)
    {
        $this->addSalesRuleIdColumnOperation->execute($setup);
    }
}
