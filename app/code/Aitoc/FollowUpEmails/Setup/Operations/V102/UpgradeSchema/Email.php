<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Setup\Operations\V102\UpgradeSchema;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\FollowUpEmails\Setup\Operations\V102\UpgradeSchema\Email\AlterScheduledAt as AlterScheduledAtOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class Email
 */
class Email implements UpgradeSchemaOperationInterface
{
    /**
     * @var AlterScheduledAtOperation
     */
    private $alterScheduledAtOperation;

    /**
     * Email constructor.
     * @param AlterScheduledAtOperation $alterScheduledAtOperation
     */
    public function __construct(AlterScheduledAtOperation $alterScheduledAtOperation)
    {
        $this->alterScheduledAtOperation = $alterScheduledAtOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->alterScheduledAt($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function alterScheduledAt(SchemaSetupInterface $setup)
    {
        $this->alterScheduledAtOperation->execute($setup);
    }
}
