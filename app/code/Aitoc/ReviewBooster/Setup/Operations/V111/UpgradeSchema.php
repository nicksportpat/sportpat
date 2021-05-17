<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V111;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V111\UpgradeSchema\ReviewDetailsTable as UpgradeReviewDetailTableSchemaOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaOperationInterface
{
    /**
     * @var UpgradeReviewDetailTableSchemaOperation
     */
    private $upgradeReviewDetailsTableSchemaOperation;

    /**
     * UpgradeSchema constructor.
     * @param UpgradeReviewDetailTableSchemaOperation $reviewDetailTableOperation
     */
    public function __construct(UpgradeReviewDetailTableSchemaOperation $reviewDetailTableOperation)
    {
        $this->upgradeReviewDetailsTableSchemaOperation = $reviewDetailTableOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->upgradeReviewDetailsTableSchema($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeReviewDetailsTableSchema(SchemaSetupInterface $setup)
    {
        $this->upgradeReviewDetailsTableSchemaOperation->execute($setup);
    }
}
