<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Setup\Operations\V102;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\FollowUpEmails\Setup\Operations\V102\UpgradeSchema\Email as UpgradeEmailTableSchemaOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaOperationInterface
{
    /**
     * @var UpgradeEmailTableSchemaOperation
     */
    private $upgradeEmailTableSchemaOperation;

    /**
     * UpgradeSchema constructor.
     * @param UpgradeEmailTableSchemaOperation $upgradeEmailTableSchemaOperation
     */
    public function __construct(
        UpgradeEmailTableSchemaOperation $upgradeEmailTableSchemaOperation
    ) {
        $this->upgradeEmailTableSchemaOperation = $upgradeEmailTableSchemaOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->upgradeEmailTableSchema($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeEmailTableSchema(SchemaSetupInterface $setup)
    {
        $this->upgradeEmailTableSchemaOperation->execute($setup);
    }
}
